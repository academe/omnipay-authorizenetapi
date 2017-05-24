<?php

namespace Omnipay\AuthorizeNetApi\Message;

/**
 *
 */

use Academe\AuthorizeNetObjects\Request\Transaction\AuthOnly;
use Academe\AuthorizeNetObjects\Request\CreateTransaction;
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

        return new CreateTransaction($this->getAuth(), $transaction);
    }

    /**
     *
     */
    public function sendData($data)
    {
    }
}
