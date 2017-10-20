<?php

namespace Omnipay\AuthorizeNetApi\Message;

/**
 * Fetch a transaction by transactionId or transactionReference.
 */

//use Academe\AuthorizeNet\Request\GetHostedPaymentPage;
//use Academe\AuthorizeNet\Collections\HostedPaymentSettings;
//use Academe\AuthorizeNet\Request\Model\HostedPaymentSetting;

use Academe\AuthorizeNet\Request\GetTransactionDetails;

class FetchTransactionRequest extends AbstractRequest
{
    /**
     * @returns GetTransactionDetails
     */
    public function getData()
    {
        $request = new GetTransactionDetails(
            $this->getAuth(),
            $this->getTransactionReference()
        );

        if ($this->getTransactionId()) {
            $request = $request->withRefId($this->getTransactionId());
        }

        return $request;
    }

    /**
     * Accept a transaction and sends it as a request.
     *
     * @param $data TransactionRequestInterface
     * @returns TransactionResponse
     */
    public function sendData($data)
    {
        // Send the request to the gateway.
        $response_data = $this->sendMessage($data);

        // We should be getting a transactino back.
        // TODO: however, there are a few additional fields to add, such as
        // event timestamps and batch details, authorised amount vs settled
        // amount, line items, final billing, shipping deails, etc.
        // In fact, the overall structure is very different, even though it
        // is constructed largely of the same buidling blocks.

        return new AuthorizeResponse($this, $response_data);
    }
}
