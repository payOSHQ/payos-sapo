# INSTALL

```
composer install
```

- Create .env and go to my.payos.vn. After that, Please add your payment-gateway key in .env
- Go to Sapo admin and create new Sapo key which allow to read and write order
- In Sapo admin, go to this path "./admin/settings/checkout" and add this script in "Xu li don hang"

```


<script src="https://dev.sapo.payos.vn/checkout.php"></script>

```

- Go to path "/admin/settings/metafields/order" add add 2 metafields (only if you need to show this information in order page):

| Name               | Type                  | Namespace and Key  |
| ------------------ | --------------------- | ------------------ |
| payOS checkout URL | URL                   | payos.checkout_url |
| payOS transaction  | Multi line text field | payos.transaction  |

# RUN APP LOCAL

```
 php -S localhost:8000 -t public
```
