<?php

namespace Omnipay\AuthorizeNetApi;

/**
 *
 */

use Omnipay\Common\Exception\InvalidRequestException;

use Omnipay\AuthorizeNetApi\Message\AuthorizeRequest;
use Omnipay\AuthorizeNetApi\Message\PurchaseRequest;
use Omnipay\AuthorizeNetApi\Message\VoidRequest;
use Omnipay\AuthorizeNetApi\Message\RefundRequest;

class ApiGateway extends AbstractGateway
{
    /**
     * The common name for this gateway driver API.
     */
    public function getName()
    {
        return 'Authorize.Net API';
    }

    /**
     * The authorization transaction.
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest(
            AuthorizeRequest::class,
            $parameters
        );
    }

    /**
     * The purchase transaction.
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest(
            PurchaseRequest::class,
            $parameters
        );
    }

    /**
     * Void an authorized transaction.
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest(
            VoidRequest::class,
            $parameters
        );
    }

    /**
     * Refund a captured transaction (before it is cleared).
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest(
            RefundRequest::class,
            $parameters
        );
    }
}
