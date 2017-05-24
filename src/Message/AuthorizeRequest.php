<?php

namespace Omnipay\AuthorizeNetApi\Message;

/**
 *
 */

use Academe\AuthorizeNetObjects\Request\Transaction\AuthOnly;
use Academe\AuthorizeNetObjects\Amount\MoneyPhp;
use Money\Currency;
use Money\Money;

class AuthorizeRequest extends AbstractRequest
{
    /**
     * Return the complete message object.
     */
    public function getData()
    {
        $amount = new MoneyPhp($this->getMoney());

        $transaction = new AuthOnly($amount);

        $transaction = $transaction->with([
            // ...other data here...
        ]);

        return $transaction;
    }

    /**
     * Accept a transaction and send it as a request.
     */
    public function sendData($data)
    {
        $response = $this->sendTransaction($data);

        // TODO: here put the result into the appropriate Response class.
        // It's a Response we need to return, not just data.

        return $response;
    }
}
