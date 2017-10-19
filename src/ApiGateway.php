<?php

namespace Omnipay\AuthorizeNetApi;

/**
 *
 */

use Omnipay\Common\Exception\InvalidRequestException;

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
            \Omnipay\AuthorizeNetApi\Message\AuthorizeRequest::class,
            $parameters
        );
    }

    /**
     * The purchase transaction.
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest(
            \Omnipay\AuthorizeNetApi\Message\PurchaseRequest::class,
            $parameters
        );
    }

    /**
     * The capture transaction.
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest(
            \Omnipay\AuthorizeNetApi\Message\CaptureRequest::class,
            $parameters
        );
    }

    /**
     * Fetch a transaction.
     */
    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest(
            \Omnipay\AuthorizeNetApi\Message\FetchTransactionRequest::class,
            $parameters
        );
    }
}
