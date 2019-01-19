<?php

namespace Omnipay\AuthorizeNetApi;

use Omnipay\Tests\TestCase;
use Omnipay\Common\CreditCard;

class AuthorizeRequestTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new ApiGateway(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
    }

    public function testOpaqueData()
    {
        $opaqueDescriptor = 'COMMON.ACCEPT.INAPP.PAYMENT';
        $opaqueValue = str_shuffle(str_repeat('1234567890ABCDEFGHIJ', 10));

        $cardToken = $opaqueDescriptor . ':' . $opaqueValue;

        $request = $this->gateway->authorize([
            'opaqueDataDescriptor' => $opaqueDescriptor,
            'opaqueDataValue' => $opaqueValue,
        ]);

        $this->assertSame($cardToken, $request->getToken());

        $this->assertSame($opaqueDescriptor, $request->getOpaqueDataDescriptor());
        $this->assertSame($opaqueValue, $request->getOpaqueDataValue());
    }

    public function testCardToken()
    {
        $opaqueDescriptor = 'COMMON.ACCEPT.INAPP.PAYMENT';
        $opaqueValue = str_shuffle(str_repeat('1234567890ABCDEFGHIJ', 10));

        $cardToken = $opaqueDescriptor . ':' . $opaqueValue;

        $request = $this->gateway->authorize([
            'token' => $cardToken,
        ]);

        $this->assertSame($cardToken, $request->getToken());

        $this->assertSame($opaqueDescriptor, $request->getOpaqueDataDescriptor());
        $this->assertSame($opaqueValue, $request->getOpaqueDataValue());
    }

    public function testCustomerData()
    {
        $request = $this->gateway->authorize([
            'amount' => 1.23,
            'customerId' => 'customerId',
            'customerType' => 'individual',
            'customerTaxId' => 'customerTaxId',
            'customerDriversLicense' => 'customerDriversLicense',
            'card' => new CreditCard([
                'email' => 'email@example.com',
            ]),
        ]);

        // The request data will have a customer object with this data in.

        $this->assertArraySubset(
            [
                'id' => 'customerId',
                'type' => 'individual',
                'email' => 'email@example.com',
                'driversLicense' => 'customerDriversLicense',
                'taxId' => 'customerTaxId',
            ],
            $request->getData()->getCustomer()->jsonSerialize()
        );
    }
}
