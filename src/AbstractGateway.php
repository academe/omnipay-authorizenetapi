<?php

namespace Omnipay\AuthorizeNetApi;

/**
 *
 */

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\AbstractGateway as OmnipayAbstractGateway;

abstract class AbstractGateway extends OmnipayAbstractGateway
{
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
            // The shared key used to sign notifications.
            'signatureKey' => '',
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
     * The shared signature key.used to sign notifications sent by the
     * webhooks in the X-Anet-Signature HTTP header.
     * Only needed when receiving a notification.
     * Optional; the signature hash will only be checked if the signature
     * is supplied.
     */
    public function setSignatureKey($value)
    {
        if (!is_string($value)) {
            throw new InvalidRequestException('Signature Key must be a string.');
        }

        return $this->setParameter('signatureKey', $value);
    }

    public function getSignatureKey()
    {
        return $this->getParameter('signatureKey');
    }

    /**
     * The capture transaction.
     */
    public function capture(array $parameters = [])
    {
        return $this->createRequest(
            \Omnipay\AuthorizeNetApi\Message\CaptureRequest::class,
            $parameters
        );
    }

    /**
     * Fetch a transaction.
     */
    public function fetchTransaction(array $parameters = [])
    {
        return $this->createRequest(
            \Omnipay\AuthorizeNetApi\Message\FetchTransactionRequest::class,
            $parameters
        );
    }

    /**
     * Handle notifcation server requests (webhooks).
     */
    public function acceptNotification(array $parameters = [])
    {
        return $this->createRequest(
            \Omnipay\AuthorizeNetApi\Message\AcceptNotification::class,
            $parameters
        );
    }
}
