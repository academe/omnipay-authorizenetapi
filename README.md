
[![Build Status](https://travis-ci.org/academe/Omnipay-AuthorizeNetApi.svg?branch=master)](https://travis-ci.org/academe/Omnipay-AuthorizeNetApi)
[![Latest Stable Version](https://poser.pugx.org/academe/omnipay-authorizenetapi/v/stable)](https://packagist.org/packages/academe/omnipay-authorizenetapi)
[![Total Downloads](https://poser.pugx.org/academe/omnipay-authorizenetapi/downloads)](https://packagist.org/packages/academe/omnipay-authorizenetapi)
[![Latest Unstable Version](https://poser.pugx.org/academe/omnipay-authorizenetapi/v/unstable)](https://packagist.org/packages/academe/omnipay-authorizenetapi)
[![License](https://poser.pugx.org/academe/omnipay-authorizenetapi/license)](https://packagist.org/packages/academe/omnipay-authorizenetapi)

Table of Contents
=================

   * [Omnipay-AuthorizeNetApi](#omnipay-authorizenetapi)
   * [Authorize.Net API](#authorizenet-api)
      * [API Authorize/Purchase (Credit Card)](#api-authorizepurchase-credit-car                           d)
      * [API Capture](#api-capture)
      * [API Authorize/Purchase (Opaque Data)](#api-authorizepurchase-opaque-dat                           a)
      * [API Void](#api-void)
      * [API Refund](#api-refund)
      * [API Fetch Transaction](#api-fetch-transaction)
   * [Hosted Payment Page](#hosted-payment-page)
      * [Hosted Payment Page Authorize/Purchase](#hosted-payment-page-authorizep                           urchase)

# Omnipay-AuthorizeNetApi

Omnipay 3.x implementation of Authorize.Net API

# Authorize.Net API

The *Authorize.Net API* driver handles server-to-server requests.
It is used both for direct card payment (though check PCI requirements)
and for creating transactions using a card token.

## API Authorize/Purchase (Credit Card)

The following example is a simple authorize with supplied card details.
You would normally avoid allowing card details near your merchanet site
back end for PCI compliance reasons,
supplying a tokenised card reference instead (see later section for this).

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

## API Capture

Once authorized, the amount can be captured:

```php
// Captured from the authorization response.
$transactionReference = $response->getTransactionReference();

$response = $gateway->capture([
    'amount' => '7.99',
    'currency' => 'USD',
    'transactionReference' => $transactionReference,
])->send();
```

## API Authorize/Purchase (Opaque Data)

The "Opaque Data" here is a tokenised credit or debit card.
Authorize.Net can tokenise cards in a number of ways, once of which
is through the `accept.js` package on the front end. It works like this:

You build a payment form in your page.
As well as hard-coding it as shown below, the gateway provides a method
to generate it dynamically too.

```html
<form id="paymentForm"
    method="POST"
    action="https://example.com/authorize">
    <input type="text" name="cardNumber" id="cardNumber" placeholder="cardNumber"/>
    <input type="text" name="expMonth" id="expMonth" placeholder="expMonth"/>
    <input type="text" name="expYear" id="expYear" placeholder="expYear"/>
    <input type="text" name="cardCode" id="cardCode" placeholder="cardCode"/>
    <input type="hidden" name="dataValue" id="dataValue" />
    <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
    <button>Pay Now</button>
</form>
```

Note the card detail elements do not have names, so will not be submitted
to your site.
Two hidden fields are defined to carry the opaquer data to your site.
You can include any many other fields as you like in the same form,
which may include names and an address.

After the payment form, you will need the `accept.js` JavaScript:

```javascript
    <script type="text/javascript"
        src="https://jstest.authorize.net/v1/Accept.js"
        charset="utf-8">\
    </script>
```

Or use `https://js.authorize.net/v1/Accept.js` for production.

You need to catch the "Pay Now" submission and send it to a function to
process the card details. Either an `onclick` attribute or a jQuery event
will work. For example:

    <button type="button" onclick="sendPaymentDataToAnet()">Pay</button>

The `sendPaymentDataToAnet` function handles the tokenisation.

```
<script type="text/javascript">
function sendPaymentDataToAnet() {
    // Set up authorisation to access the gateway.
    var authData = {};
        authData.clientKey = "YOUR PUBLIC CLIENT KEY";
        authData.apiLoginID = "YOUR API LOGIN ID";

    // Capture the card details from the payment form.
    // The cardCode is the CVV.
    // You can include fullName and zip fields too, for added security.
    // You can pick up bank account fields in a similar way, if using
    // that payment method.
    var cardData = {};
        cardData.cardNumber = document.getElementById("cardNumber").value;
        cardData.month = document.getElementById("expMonth").value;
        cardData.year = document.getElementById("expYear").value;
        cardData.cardCode = document.getElementById("cardCode").value;

    // Now send the card data to the gateway for tokenisation.
    // The responseHandler function will handle the response.
    var secureData = {};
        secureData.authData = authData;
        secureData.cardData = cardData;
        Accept.dispatchData(secureData, responseHandler);
}
</script>
```

The response handler is able to provide errors that may have been
generated while trying to tokenise the card.
But if all is well, it updates the payment form with the opaque data
(another function `paymentFormUpdate`):

```javascript
function responseHandler(response) {
    if (response.messages.resultCode === "Error") {
        var i = 0;
        while (i < response.messages.message.length) {
            console.log(
                response.messages.message[i].code + ": " +
                response.messages.message[i].text
            );
            i = i + 1;
        }
    } else {
        paymentFormUpdate(response.opaqueData);
    }
}
```

Populate the opaque data hidden form items, then submit the form again:

```javascript
function paymentFormUpdate(opaqueData) {
    document.getElementById("dataDescriptor").value = opaqueData.dataDescriptor;
    document.getElementById("dataValue").value = opaqueData.dataValue;
    document.getElementById("paymentForm").submit();
}
```

Back at the server, you will have two opaque data fields to capture:

* dataDescriptor
* dataValue

Initiate an `authorize()` or `purchase()` at the backend, as described in
the previous section. In the `creditCard` object, leave the card details
blank, not set. Instead, send the opaque data:

```php
$gateway->authorize([
    ...
    'opaqueDataDescriptor' => $opaqueDataDescriptor,
    'opaqueDataValue' => $opaqueDataValue,
]);
```

The authorizatiob or purchase should then go ahead as though the card
details were provided directly. In the result, the last four digits
of the card will be made available in case a refund needs to be performed.

Further details can be 
[fouund in the officual documentation](https://developer.authorize.net/api/reference/features/acceptjs.html).

## API Void

An authorized transaction can be voided:

```php
// Captured from the authorization response.
$transactionReference = $response->getTransactionReference();

$response = $gateway->void([
    'transactionReference' => $transactionReference,
])->send();
```

## API Refund

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

## API Fetch Transaction

An existing transaction can be fetched from the gateway given
its `transactionReference`:

```php
$response = $gateway->fetchTransaction([
    'transactionReference' => $transactionReference,
])->send();
```

The Hosted Payment Page will host the payment form on the gateway.
The form can be presented to the user as a full page redirect or in an iframe.

# Hosted Payment Page

The Hosted Payment Page is a different gateway:

```php
$gateway = Omnipay\Omnipay::create('AuthorizeNetApi_HostedPage');
```

The gateway is configured the same way as the direct API gateway,
and the authorize/purchase
requests are created in the same way, except for the addition of
`return` and `cancel` URLs:

## Hosted Payment Page Authorize/Purchase

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
name, then append with the specific option you want to set, and use the
result as the parameter, keeping the name in *camelCase*.
So the above set of options are supported by the following parameters:

* paymentOptionsCardCodeRequired
* paymentOptionsShowCreditCard
* paymentOptionsShowBankAccount

You can set these in the `authorize()` stage:

```php
$request = $gateway->authorize([
    ...
    // Hide the bank account form but show the credit card form.
    'paymentOptionsShowCreditCard' => true,
    'paymentOptionsShowBankAccount' => false,
    // Change the "Pay" buton text.
    'buttonOptionsText' => 'Pay now',
]);
```

or use the `set*()` form to do the same thing:

    $request->setPaymentOptionsShowBankAccount(false);

