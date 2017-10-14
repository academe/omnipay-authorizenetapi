<?php

namespace Omnipay\AuthorizeNetApi\Message;

/**
 *
 */

//use Academe\AuthorizeNet\Request\Transaction\AuthOnly;
//use Academe\AuthorizeNet\Amount\MoneyPhp;
//use Money\Currency;
//use Money\Money;
use Omnipay\Common\Message\RequestInterface;
use Academe\AuthorizeNet\Response\Response;

class AuthorizeResponse extends AbstractResponse
{
    /**
     * The overall transaction response codes.
     * PEDNING is "Held for Review".
     */
    const RESPONSE_CODE_APPROVED    = 1;
    const RESPONSE_CODE_DECLINED    = 2;
    const RESPONSE_CODE_ERROR       = 3;
    const RESPONSE_CODE_PEDNING     = 4;

    public function __construct(RequestInterface $request, $data)
    {
        // Parse the request.
        parent::__construct($request, $data);
    }

    /**
     * Tells us whether the transaction is successful and complete.
     * There must be no overall error, and the transaction must be approved.
     */
    public function isSuccessful()
    {
        // Note the loose comparison because the API returns strings for
        // all numbers in the JSON response, but integers in the XML response (see
        // https://api.authorize.net/xml/v1/schema/AnetApiSchema.xsd where we have
        // <xs:element name="responseCode" type="xs:int"/>).
        // So I don't trust the data type we get back, and we will play loose and
        // fast with implicit conversions here.

        return $this->responseIsSuccessful()
            && $this->getResponseCode() == static::RESPONSE_CODE_APPROVED;
    }

    /** 
     * Tells us whether the transaction is pending or not.
     */
    public function isPending()
    {
        return $this->responseIsSuccessful()
            && $this->getResponseCode() == static::RESPONSE_CODE_PENDING;
    }

    /**
     * Collection of transaction message objects, or null if there are none.
     *
     * TODO: maybe we should always return the collection, whether there are any
     * transaction messages or not?
     */
    public function getTransactionMessages()
    {
        return $this->getValue('transactionResponse.transactionMessages');
    }

    public function getTransactionErrors()
    {
        return $this->getValue('transactionResponse.errors');
    }

    /**
     * Get the transaction response code.
     */
    public function getResponseCode()
    {
        return $this->getValue('transactionResponse.responseCode');
    }
}
