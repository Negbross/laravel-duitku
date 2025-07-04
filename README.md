# Laravel Duitku
Laravel Duitku is package to provide payment gateway Duitku.

### Laravel Installation Instructions
1. From your projects root folder in terminal run:

   ```bash
   composer require negbross/laravel-duitku
   ```

2. Install config:

   ```bash
   php artisan duitku:install
   ```

### Configuration
Laravel Duitku can be configured in directly in `/config/duitku.php`. Add variables to your `.env` file.


##### Environment File
Here are the `.env` file variables available:

```dotenv
DUITKU_ENV=development
DUITKU_MERCHANT_CODE=xxxxxxx
DUITKU_API_KEY=xxxxxx
DUITKU_CALLBACK_URL=https://example.com/callback
DUITKU_RETURN_URL=https://example.com/return
```

### Usage API

##### Get Payment Method
example:

```php
use Negbross\LaravelDuitku\Facades\DuitkuAPI;

$listPayments = DuitkuAPI::getPaymentMethod(1000000);
```

##### Create Payment
example:

```php
use Negbross\LaravelDuitku\Facades\DuitkuAPI;

DuitkuAPI::createTransaction([
    'merchantOrderId'   => 10000,
    'customerVaName'    => 'Super negbros',
    'email'             => 'email@example.com',
    'paymentAmount'     => 100000,
    'paymentMethod'     => 'VC',
    'productDetails'    => 'Buy Ferrari',
    'expiryPeriod'      => 10,  // optional (minute)
    'phoneNumber'       => '08123456789', // optional
    'itemDetails'       => [ // optional
        [
            'name' => 'Test Item 1',
            'price' => 10000,
            'quantity' => 1
        ],[
            'name' => 'Test Item 2',
            'price' => 10000,
            'quantity' => 1
        ]
    ],
    'customerDetail'    => [ // optional
        'firstName'         => 'Super',
        'lastName'          => 'Negbros',
        'email'             => 'email@example.com',
        'phoneNumber'       => $phoneNumber,
        'billingAddress'    => $address,
        'shippingAddress'   => $address
    ],
    'additionalParam'   => '', // optional
    'merchantUserInfo'  => '', // optional
]);
```
[List Payment Method](https://docs.duitku.com/api/id/#metode-pembayaran)

##### Check Transaction
example:

```php
use Negbross\LaravelDuitku\Facades\DuitkuAPI;

DuitkuAPI::checkTransactionStatus(1000000);
```

##### Handle Callback Transaction
example:

```php
use Negbross\LaravelDuitku\Facades\DuitkuAPI;

$payment = DuitkuAPI::getNotificationTransaction();
```

### Usage POP

##### Create Payment
example:

```php
use Negbross\LaravelDuitku\Facades\DuitkuPOP;

DuitkuPOP::createTransaction([
    'merchantOrderId'   => 10000,
    'customerVaName'    => 'Super negbros',
    'email'             => 'email@example.com',
    'paymentAmount'     => 100000,
    'productDetails'    => 'Buy Company',
    'expiryPeriod'      => 10,  // optional (minute)
    'phoneNumber'       => '08123456789', // optional
    'itemDetails'       => [ // optional
        [
            'name' => 'Test Item 1',
            'price' => 10000,
            'quantity' => 1
        ],[
            'name' => 'Test Item 2',
            'price' => 10000,
            'quantity' => 1
        ]
    ],
    'customerDetail'    => [ // optional
        'firstName'         => 'Super',
        'lastName'          => 'Negbross',
        'email'             => 'email@example.com',
        'phoneNumber'       => $phoneNumber,
        'billingAddress'    => $address,
        'shippingAddress'   => $address
    ],
    'additionalParam'   => '', // optional
    'merchantUserInfo'  => '', // optional
]);
```
[List Payment Method](https://docs.duitku.com/pop/id/?php#payment-method)

#### Modal Pop Up Duitku JS

* Production
```
<script src="https://app-prod.duitku.com/lib/js/duitku.js"></script>
```

* Sandbox
```
<script src="https://app-sandbox.duitku.com/lib/js/duitku.js"></script>
```

* Implement
```
checkout.process("DXXXXS875LXXXX32IJZ7", {
    defaultLanguage: "id", //optional
    successEvent: function(result){
        console.log(result);
        alert('Payment Success');
    },
    pendingEvent: function(result){
        console.log(result);
        alert('Payment Pending');
    },
    errorEvent: function(result){
        console.log(result);
        alert('Payment Error');
    },
    closeEvent: function(result){
        console.log(result);
        alert('customer closed the popup without finishing the payment');
    }
});
```

##### Handle Callback Transaction
example:

```php
use Negbross\LaravelDuitku\Facades\DuitkuPOP;

$payment = DuitkuPOP::getNotificationTransaction();
```

## License

This Package is licensed under the MIT license. Enjoy!
