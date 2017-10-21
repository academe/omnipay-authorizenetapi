<?php

namespace Omnipay\AuthorizeNetApi\Message;

/**
 * The features of the basic envelope for all responses.
 */

use Omnipay\Common\Message\AbstractResponse as OmnipayAbstractResponse;
use Omnipay\Common\Message\RequestInterface;

use Academe\AuthorizeNet\Response\Response;

use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

abstract class AbstractResponse extends OmnipayAbstractResponse
{
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
        // Omnipay Common has some data to record.
        parent::__construct($request, $data);

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
     * The transactionId is returned only if sent in the request.
     */
    public function getTransactionId()
    {
        return $this->getRefId();
    }

    /**
     * Get the first top-level result code.
     * Note this will be unsuitable for most transactions, as the response can
     */
    public function getResultCode()
    {
        return $this->getValue('resultCode');
    }

    /**
     * Get the first top-level message text.
     */
    public function getResponseMessage()
    {
        return $this->getValue('messages.first.text');
    }

    /**
     * Get the transaction message text from the response envelope.
     * Inheriting responses will normally refine this to look deeper into
     * the response body.
     */
    public function getMessage()
    {
        return $this->getResponseMessage();
    }

    /**
     * Get the first top-level message code.
     */
    public function getResponseCode()
    {
        return $this->getValue('messages.first.code');
    }

    /**
     * Return the message code from the response envelope.
     * Inheriting responses will normally refine this to look deeper into
     * the response body.
     */
    public function getCode()
    {
        return $this->getResponseCode();
    }

    /**
     * Get all top-level response message collection.
     */
    public function getResponseMessages()
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
        return $this->getResultCode() === Response::RESULT_CODE_OK;
    }

    public function isSuccessful()
    {
        return $this->getResultCode() === Response::RESULT_CODE_OK;
    }
}
