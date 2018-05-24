
[![Build Status](https://travis-ci.org/academe/Omnipay-AuthorizeNetApi.svg?branch=master)](https://travis-ci.org/academe/Omnipay-AuthorizeNetApi)
[![Latest Stable Version](https://poser.pugx.org/academe/omnipay-authorizenetapi/v/stable)](https://packagist.org/packages/academe/omnipay-authorizenetapi)
[![Total Downloads](https://poser.pugx.org/academe/omnipay-authorizenetapi/downloads)](https://packagist.org/packages/academe/omnipay-authorizenetapi)
[![Latest Unstable Version](https://poser.pugx.org/academe/omnipay-authorizenetapi/v/unstable)](https://packagist.org/packages/academe/omnipay-authorizenetapi)
[![License](https://poser.pugx.org/academe/omnipay-authorizenetapi/license)](https://packagist.org/packages/academe/omnipay-authorizenetapi)

# Omnipay-AuthorizeNetApi

Omnipay 3.x implementation of Authorize.Net API

# Development Example

This is under development, but is usable within limitations.

The following example is a simple authorize with known card details.
You would normally avoid this particular method for PCI compliance reasons,
supplying a tokenised card reference instead.

```php
<?php

include 'vendor/autoload.php';

$gateway = Omnipay\Omnipay::create('AuthorizeNetApi_Api');

$gateway->setAuthName('XXXXXxxxxxx');
$gateway->setTransactionKey('XXXXX99999xxxxx');
$gateway->setTestMode(true);

$creditCard = new Omnipay\Common\CreditCard([
    // Swiped tracks can be provided instead, if the card is present.
    'number' => '4000123412341234',
    'expiryMonth' => '12',
    'expiryYear' => '2020',
    'cvv' => '123',
    // Billing and shipping details can be added here.
]);

// Generate a unique merchant site transaction ID.
$transactionId = rand(100000000, 999999999);

$response = $gateway->authorize([
    'amount' => '7.99',
    'currency' => 'USD',
    'transactionId' => $transactionId,
    'card' => $creditCard,
])->send();

// Or use $gateway->purchase() to immediately capture.

var_dump($response->isSuccessful());
// bool(true)

var_dump($response->getCode());
// string(1) "1"

var_dump($response->getMessage());
// string(35) "This transaction has been approved."

var_dump($response->getTransactionReference());
// string(11) "60103474871"
```

If authorized, the amount can be captured:

```php
// Captured from the authorization response.
$transactionReference = $response->getTransactionReference();

$response = $gateway->capture([
    'amount' => '7.99',
    'currency' => 'USD',
    'transactionReference' => $transactionReference,
])->send();
```

An authorized transaction can be voided:

```php
// Captured from the authorization response.
$transactionReference = $response->getTransactionReference();

$response = $gateway->void([
    'transactionReference' => $transactionReference,
])->send();
```

A cleared credit card payment can be refunded, given the original
transaction reference, the original amount, and the last four digits
of the credit card:

```php
$response = $gateway->refund([
    'amount' => '7.99',
    'currency' => 'USD',
    'transactionReference' => $transactionReference,
    'numberLastFour' => '1234',
])->send();
```

An existing transaction can be fetched from the gateway given
its `transactionReference`:

```php
$response = $gateway->fetchTransaction([
    'transactionReference' => $transactionReference,
])->send();
```

The Hosted Payment Page will host the payment form on the gateway.
The form can be presented to the user as a full page redirect or in an iframe.

The Hosted Payment Page is a different gateway:

```php
$gateway = Omnipay\Omnipay::create('AuthorizeNetApi_HostedPage');
```

The gateway is configured the same way, and the authorize/purchase
requests are created in the same way, except for the return and cancel URLs:

```php
$request = $gateway->authorize([
    'amount' => $amount,
    // etc.
    'returnUrl' => 'return URL after the transaction is approved or rejected',
    'cancelUrl' => 'URL to use if the user cancels the transaction',
]);
```

The response will be a redirect, with the following details used to
construct the redirect in the merchant site:

```php
$response = $request->send();

$response->getRedirectMethod();
// Usually "POST"

$response->getRedirectUrl();
// The redirect URL or POST form action.

$response->getRedirectData()
// Array of name/value elements used to construct hidden fields
// in the POST form.
```

A naive POST "pay now" button may look like the following form.

```php
$method = $response->getRedirectMethod();
$action = $response->getRedirectUrl();

echo "<form method='$method' action='$action'>";
foreach ($response->getRedirectData() as $name => $value) {
    $dataName = htmlspecialchars($name);
    $dataValue = htmlspecialchars($value);

    echo "<input type='hidden' name='$dataName' value='$dataValue' />";
}
echo "<button type='submit'>Pay Now</button>";
echo "</form>";
```

This will take the user to the gateway payment page, looking something
like this by default:

------
![Default Gateway Payment Page](docs/authorizenet-default-payment-form.png)
------

The billing details will be prefilled with the card details supplied
in the `$gateway->authorize()`.
What the user can change and/or see, can be changed using options or
confiration in the account.

Taking the `hostedPaymentPaymentOptions` as an example,
this is how the options are set:

The [documentation](https://developer.authorize.net/api/reference/features/accept_hosted.html)
lists `hostedPaymentPaymentOptions` as supporting these options:
`{"cardCodeRequired": false, "showCreditCard": true, "showBankAccount": true}`

To set any of the options, drop the `hostedPayment` prefix from the options
name, then add the specific option you want to set, and use the
result as the parameter.
So the above set of options are supported by the following parameters:

* paymentOptionsCardCodeRequired
* paymentOptionsShowCreditCard
* paymentOptionsShowBankAccount

Set these in tha `authorize()` stage:

```php
$gateway->authorize([
    ...
    // Hide the bank account form but show the credit card form.
    'paymentOptionsShowCreditCard' => true,
    'paymentOptionsShowBankAccount' => false,
    // Change the "Pay" buton text.
    'buttonOptionsText' => 'Pay now',
]);

or use the `set*()` form to do the same thing.
