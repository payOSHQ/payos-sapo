<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Utils\PayOSHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Utils\Sapo;
use Exception;

class WebhookTransaction
{
  public function __invoke(Request $request, Response $response, array $args): Response
  {
    $sapo = new Sapo();
    $payOS = (new PayOSHandler())->PayOS();

    try {
      $contentType = $request->getHeaderLine('Content-Type');
      if (!strstr($contentType, 'application/json')) {
        throw new Exception('Content is not in JSON');
      }
      $contents = json_decode(file_get_contents('php://input'), true);
      if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Parse JSON failed');
      }

      $request = $request->withParsedBody($contents);
      $body = $request->getParsedBody();
      $payOS->verifyPaymentWebhookData($body);
      // check demo data when confirm hook
      if ($body['data']['accountNumber'] === '12345678' && $body['data']['reference'] === 'TF230204212323') {
        return $response;
      }
      $data = $body['data'];
      $orderCode = $data['orderCode'];
      $sapoOrder = $sapo->getOrderById($orderCode);
      if (!$sapoOrder || !isset($sapoOrder['order'])) {
        throw new Exception('Not found order');
      }
      if ($sapoOrder['order']['financial_status'] === SAPO_ORDER_PAID_MESSAGE) {
        return $response;
      }
      // confirm order
      $sapo->confirmOrder($orderCode);

      // update note
      $tranNote = '  ^^^^^^^ Số dư tài khoản vừa tăng ' . $data['amount'] . 'VND vào ' . $data['transactionDateTime'] . ' Mô tả ' . $data['description'] . ' Mã tham chiếu ' . $data['reference'] . ' Số tài khoản ' . $data['accountNumber'];
      $sapo->updateNoteOrder($orderCode, $sapoOrder['order']['note'] . $tranNote);

      return $response;
    } catch (Exception $e) {
      error_log(var_export($e->getMessage(), true));
      $response->getBody()->write(json_encode(['error' => $e->getMessage(), 'code' => $e->getCode()]));
      return $response->withStatus(400);
    }
  }
}
