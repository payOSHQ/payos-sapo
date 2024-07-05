<?php
declare(strict_types=1);

namespace App\Utils;

use Exception;
use App\Utils\Util;

class Sapo
{
  protected $headers;
  protected $originUrl;

  public function __construct()
  {
    $this->originUrl = "https://" . $_ENV['SAPO_API_KEY'] . ":" . $_ENV['SAPO_API_SECRET'] . '@' . $_ENV['SAPO_HOST_NAME'];
    $this->headers = array(
      'Content-Type: application/json'
    );
  }
  public function getOrderById($orderId)
  {
    $options = [
      'http' => [
        'header' => implode("\r\n", $this->headers),
        'ignore_errors' => true
      ]
    ];
    $context = stream_context_create($options);
    $apiResponse = file_get_contents($this->originUrl . '/admin/orders/' . $orderId . '.json', false, $context);
    $http_response_header = $http_response_header ?? [];
    $http_code = Util::getHttpCodeFromHeaders($http_response_header);

    if ($http_code >= 400) {
      throw new Exception($apiResponse . $http_code, $http_code);
    }

    return json_decode($apiResponse, true);
  }
  public function confirmOrder($orderId, $reference)
  {
    $options = [
      'http' => [
        'header' => implode("\r\n", $this->headers),
        'ignore_errors' => true,
        'method' => 'POST',
        'content' => json_encode([
          'transaction' => [
            'kind' => 'capture',
            'payment_details' => [
              'reference' => $reference,
              'transactionInfo' => $reference
            ]
          ]
        ])
      ]
    ];
    $context = stream_context_create($options);
    $apiResponse = file_get_contents($this->originUrl . '/admin/orders/' . $orderId . '/transactions.json', false, $context);
    $http_response_header = $http_response_header ?? [];
    $http_code = Util::getHttpCodeFromHeaders($http_response_header);

    if ($http_code >= 400) {
      throw new Exception($apiResponse . $http_code, $http_code);
    }

    return json_decode($apiResponse, true);
  }
  public function updateNoteOrder($orderId, $note)
  {
    $options = [
      'http' => [
        'header' => implode("\r\n", $this->headers),
        'ignore_errors' => true,
        'method' => 'PUT',
        'content' => json_encode([
          'order' => [
            'id' => $orderId,
            'note' => $note
          ]
        ])
      ]
    ];
    $context = stream_context_create($options);
    $apiResponse = file_get_contents($this->originUrl . '/admin/orders/' . $orderId . '.json', false, $context);
    $http_response_header = $http_response_header ?? [];
    $http_code = Util::getHttpCodeFromHeaders($http_response_header);

    if ($http_code >= 400) {
      throw new Exception($apiResponse . $http_code, $http_code);
    }

    return json_decode($apiResponse, true);
  }
}