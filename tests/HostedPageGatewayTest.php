<?php

namespace Omnipay\AuthorizeNetApi;

use Omnipay\Tests\GatewayTestCase;

class HostedPageGatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new ApiGateway(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
    }
}
