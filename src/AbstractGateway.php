<?php

namespace Omnipay\AuthorizeNetApi;

/**
 *
 */

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\AbstractGateway as OmnipayAbstractGateway;
use Omnipay\AuthorizeNetApi\Traits\HasGatewayParams;

abstract class AbstractGateway extends OmnipayAbstractGateway
{
    use HasGatewayParams;

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
