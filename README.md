
# Omnipay-AuthorizeNetApi

Omnipay 3.x implementation of Authorize.Net API

# Development Example

This is under development and is not close to a production package.

You can see some initial results with the example below.
It will attempt to perform an authorisation, then dump the response as an array:

```php
<?php

include 'vendor/autoload.php';

$gateway = Omnipay\Omnipay::create('AuthorizeNetApi_Api');

$gateway->setAuthName('XXXXXxxxxxx');
$gateway->setTransactionKey('XXXXX99999xxxxx');
$gateway->setTestMode(true);

$request = $gateway->authorize([
    'amount' => '7.99',
    'currency' => 'USD',
]);

echo "<pre>";
var_dump($request->send());
```

This is an example of what you will see with the above *very* limited transaction data:

```php
array(2) {
  ["transactionResponse"]=>
  array(13) {
    ["responseCode"]=>
    string(1) "3"
    ["authCode"]=>
    string(0) ""
    ["avsResultCode"]=>
    string(1) "P"
    ["cvvResultCode"]=>
    string(0) ""
    ["cavvResultCode"]=>
    string(0) ""
    ["transId"]=>
    string(1) "0"
    ["refTransID"]=>
    string(0) ""
    ["transHash"]=>
    string(32) "2C58D98D68F97B3B47F74D2A3B8DCA24"
    ["testRequest"]=>
    string(1) "0"
    ["accountNumber"]=>
    string(0) ""
    ["accountType"]=>
    string(0) ""
    ["errors"]=>
    array(2) {
      [0]=>
      array(2) {
        ["errorCode"]=>
        string(2) "33"
        ["errorText"]=>
        string(31) "Credit card number is required."
      }
      [1]=>
      array(2) {
        ["errorCode"]=>
        string(2) "33"
        ["errorText"]=>
        string(28) "Expiration date is required."
      }
    }
    ["transHashSha2"]=>
    string(0) ""
  }
  ["messages"]=>
  array(2) {
    ["resultCode"]=>
    string(5) "Error"
    ["message"]=>
    array(1) {
      [0]=>
      array(2) {
        ["code"]=>
        string(6) "E00027"
        ["text"]=>
        string(33) "The transaction was unsuccessful."
      }
    }
  }
}
```

Note that you can get *multiple* errors in the transaction response,
but a single error in the messages response.
The whole response will be constructed of multiple objects - two of which can be
seen here (`transactionResponse` and `messages`). This will be used to put the
data into appropriate objects and wrap them together into an overall response.

