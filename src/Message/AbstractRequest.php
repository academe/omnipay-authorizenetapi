<?php

namespace Omnipay\AuthorizeNetApi\Message;

/**
 *
 */

use Omnipay\Common\Message\AbstractRequest as OmnipayAbstractRequest;
use Academe\AuthorizeNetObjects\Auth\MerchantAuthentication;

abstract class AbstractRequest extends OmnipayAbstractRequest
{
    /**
     * Get the authorisartion object.
     */
    public function getAuth()
    {
         return new MerchantAuthentication($this->getAuthName(), $this->getTransactionKey());
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
        return $this->createRequest('\Omnipay\AuthorizeNetApi\Message\AuthorizeRequest', $parameters);
    }
}
