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
    public function __construct(RequestInterface $request, $data)
    {
        // Store the raw data and request as normal.
        parent::__construct($request, $data);

        // Parse the raw data into a response message value object.
        $this->setParsedData(new Response($data));
    }

    /**
     * TBC
     */
    public function isSuccessful()
    {
    }

    public function getRefId()
    {
        return $this->getValue('refId');
    }

    public function getResultCode()
    {
        return $this->getValue('messages.resultCode');
    }

    public function getMessage()
    {
        return $this->getValue('messages[0].text');
    }

    public function getCode()
    {
        return $this->getValue('messages[0].code');
    }

    /**
     * Collection of transaction message objects, or null if there are none.
     * CHECKME: maybe we should always return the collection, whether there are any
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
}
