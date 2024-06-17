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

# RUN APP LOCAL
```
 php -S localhost:8000 -t public
```
