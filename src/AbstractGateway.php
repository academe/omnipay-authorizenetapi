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
}
