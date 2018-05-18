<?php

namespace Omnipay\AuthorizeNetApi;

/**
 *
 */

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\HasHostedPageGatewayParams;

use Omnipay\AuthorizeNetApi\Message\HostedPage\AuthorizeRequest;
use Omnipay\AuthorizeNetApi\Message\HostedPage\PurchaseRequest;

class HostedPageGateway extends AbstractGateway
{
    use HasHostedPageGatewayParams;

    /**
     * The common name for this gateway driver API.
     */
    public function getName()
    {
        return 'Authorize.Net Hosted Page';
    }

    /**
     * The authorization transaction, through a hosted page.
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest(
            AuthorizeRequest::class,
            $parameters
        );
    }

    /**
     * The purchase transaction, through a hosted page.
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest(
            PurchaseRequest::class,
            $parameters
        );
    }
}
