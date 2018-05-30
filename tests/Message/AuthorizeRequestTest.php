<?php

namespace Omnipay\AuthorizeNetApi;

use Omnipay\Tests\TestCase;

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
}
