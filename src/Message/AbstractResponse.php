<?php

namespace Omnipay\AuthorizeNetApi\Message;

/**
 *
 */

use Omnipay\Common\Message\AbstractResponse as OmnipayAbstractResponse;
use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Omnipay\Common\Message\RequestInterface;
use Academe\AuthorizeNet\Response\Response;
use Symfony\Component\PropertyAccess\PropertyAccessor;

//use Academe\AuthorizeNet\Auth\MerchantAuthentication;
//use Academe\AuthorizeNet\TransactionRequestInterface;
//use Academe\AuthorizeNet\Request\CreateTransaction;

abstract class AbstractResponse extends OmnipayAbstractResponse
{
    /**
     * Top-level response result code values.
     */
    const RESULT_CODE_OK    = 'Ok';
    const RESULT_CODE_ERROR = 'Error';

    /**
     * The reponse data parsed into nested value objects.
     */
    protected $parsedData;

    /**
     *
     */
    protected $accessor;

    public function __construct(RequestInterface $request, $data)
    {
        // Parse the raw data into a response message value object.
        $this->setParsedData(new Response($data));
    }

    /**
     * Get a value from the persed data, based on a path.
     * e.g. 'object.arrayProperty[0].stringProperty'
     * Returns null if the dependency pathis broken at any point.
     * See http://symfony.com/doc/current/components/property_access.html
     */
    public function getValue($path)
    {
        $accessor = $this->getAccessor();

        // If the accessor has not already been set, then create the default
        // accessor now.
        if (empty($accessor)) {
            $accessor = PropertyAccess::createPropertyAccessorBuilder()
                ->enableMagicCall()
                ->disableExceptionOnInvalidIndex()
                ->getPropertyAccessor();

            $this->setAccessor($accessor);
        }

        try {
            // Get the property using its path.
            // If the path breaks at any point, an exception will be
            // thrown, but we just want to return a null.

            return $accessor->getValue($this->getParsedData(), $path);
        } catch (ExceptionInterface $e) {
            return null;
        }
    }

    /**
     * Set the property accessor helper.
     */
    public function setAccessor(PropertyAccessor $value)
    {
        $this->accessor = $value;
    }

    /**
     * Get the property accessor helper.
     */
    public function getAccessor()
    {
        return $this->accessor;
    }

    /**
     * Set the data parsed into a nested value object.
     */
    public function setParsedData(Response $value)
    {
        $this->parsedData = $value;
    }

    /**
     * Get the data parsed into a nested value object.
     */
    public function getParsedData()
    {
        return $this->parsedData;
    }

    /**
     * The merchant supplied ID.
     * Up to 20 characters.
     * aka transactionId
     */
    public function getRefId()
    {
        return $this->getValue('refId');
    }

    /**
     * Get the first top-level result code.
     */
    public function getResultCode()
    {
        return $this->getValue('resultCode');

        // Equivalent to:
        //return $this->getParsedData()->getResultCode();
    }

    /**
     * Get the first top-level message text.
     */
    public function getMessage()
    {
        return $this->getValue('messages.first.text');

        // Equivalent to this, but with checks at each stage:
        //return $this->getParsedData()->getMessages()->first()->getText();
    }

    /**
     * Get the first top-level message code.
     */
    public function getCode()
    {
        return $this->getValue('messages.first.code');
    }

    /**
     * Get all top-level response messages.
     */
    public function getMessages()
    {
        return $this->getValue('messages');
    }

    /**
     * Tell us whether the response was successful overall.
     * This is just about the response as a whole; the response may
     * still represent a failed transaction.
     */
    public function responseIsSuccessful()
    {
        return $this->getResultCode() === static::RESULT_CODE_OK;
    }
}
