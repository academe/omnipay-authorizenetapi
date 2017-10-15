<?php

namespace Omnipay\AuthorizeNetApi\Message;

use Academe\AuthorizeNet\Amount\MoneyPhp;
use Academe\AuthorizeNet\AmountInterface;
use Academe\AuthorizeNet\Request\Transaction\PriorAuthCapture;
use Academe\AuthorizeNet\Request\Transaction\CaptureOnly;

class CaptureRequest extends AbstractRequest
{
    /**
     * Return the complete message object.
     */
    public function getData()
    {
        $amount = new MoneyPhp($this->getMoney());

        // Identify the original transaction being authorised.
        $refTransId = $this->getTransactionReference();

        $transaction = $this->createTransaction($amount, $refTransId);

        // TODO:
        // terminalNumber
        // order (invoiceNumber, description)

        return $transaction;
    }

    /**
     * Create a new instance of the transaction object.
     *
     * - PriorAuthCapture is used for transactios authorised through
     *   the API, e.g. a credit card authorisation.
     * - CaptureOnly is used to capture amounts authorized through
     *   other channels, such as a telephone order (MOTO).
     *
     * Only the first is supported at this time. Which gets used will
     * depend on what data is passed in.
     */
    protected function createTransaction(AmountInterface $amount, $refTransId)
    {
        return new PriorAuthCapture($amount, $refTransId);
    }

    /**
     * Accept a transaction and sends it as a request.
     *
     * @param $data TransactionRequestInterface
     * @returns TransactionResponse
     */
    public function sendData($data)
    {
        $response_data = $this->sendTransaction($data);

        return new TransactionResponse($this, $response_data);
    }
}
