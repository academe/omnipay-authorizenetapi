<?php

namespace Omnipay\AuthorizeNetApi;

/**
 *
 */

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\AbstractGateway;

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
     *
     */
    public function getDefaultParameters()
    {
        return array(
            // Required.
            // The name assigned for th application.
            'authName' => '',
            // Required.
            // The access token assigned to this application.
            'transactionKey' => '',
            // Optional.
            // Either mobileDeviceId or refId can be provided.
            'mobileDeviceId' => '',
            'refId' => '',
            // True to run against the sandbox.
            'testMode' => false,
        );
    }

    /**
     * The application auth name.
     */
    public function setAuthName($value)
    {
        if (!is_string($value)) {
            throw new InvalidRequestException('Auth name must be a string.');
        }

        return $this->setParameter('authName', $value);
    }

    public function getAuthName()
    {
        return $this->getParameter('authName');
    }

    /**
     * The application auth transaction key.
     */
    public function setTransactionKey($value)
    {
        if (!is_string($value)) {
            throw new InvalidRequestException('Transaction Key must be a string.');
        }

        return $this->setParameter('transactionKey', $value);
    }

    public function getTransactionKey()
    {
        return $this->getParameter('transactionKey');
    }

    /**
     * The authorization transaction.
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest(\Omnipay\AuthorizeNetApi\Message\AuthorizeRequest::class, $parameters);
    }

    /**
     * The authorization transaction, through a hosted page.
     * CHECKME: should we move this to a "HostedPage" API type?
     */
    public function hostedPageAuthorize(array $parameters = array())
    {
        return $this->createRequest(\Omnipay\AuthorizeNetApi\Message\HostedPageAuthorizeRequest::class, $parameters);
    }

    /**
     * The purchase transaction.
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest(\Omnipay\AuthorizeNetApi\Message\PurchaseRequest::class, $parameters);
    }

    /**
     * The capture transaction.
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest(\Omnipay\AuthorizeNetApi\Message\CaptureRequest::class, $parameters);
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
