Liq Pay Checkout 
========================
### Installation
```console
composer require yushni/liq-pay-sdk
```
## Simple usage :

##### Create LiqPay class 
```php 
$encoder = new LiqPay\Encoder\Encoder();
$liqPay = new LiqPay\LiqPay('privateKey', 'publicKey', true, $encoder);
```

##### Generate checkout url
```php
$payment = LiqPay\Action\Payment::pay(1.12, "cab95cee-48b8-423b-8979-1675bb452f13", "UAH", "Description")
$payment->setResultUrl('result_url');
$payment->setServerUrl('server_url');

$liqPay->generateCheckoutUrl($payment);
```

##### Obtain callback results
```php
$liqPay->obtainCallback('data', 'signature');
```

### Advanced usage 
##### You can make your own Action and declare it. Action must be instance of LiqPay\Action\Action class.
```php
$liqPay->addAction(LiqPay\Action\Payment, ['pay'])
```
