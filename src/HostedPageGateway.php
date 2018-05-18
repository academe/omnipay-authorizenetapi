<?php

namespace Omnipay\AuthorizeNetApi;

/**
 *
 */

use Omnipay\Common\Exception\InvalidRequestException;

use Omnipay\AuthorizeNetApi\Message\HostedPage\AuthorizeRequest;
use Omnipay\AuthorizeNetApi\Message\HostedPage\PurchaseRequest;

class HostedPageGateway extends AbstractGateway
{
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

    // Setters for various global settings.

    /**
     * Used only by the hosted payment page at this time.
     */
    public function setCancelUrl($value)
    {
        $this->setParameter('cancelUrl', $value);
    }

    /**
     * Used only by the hosted payment page at this time.
     */
    public function setReturnUrl($value)
    {
        $this->setParameter('returnUrl', $value);
    }
}
