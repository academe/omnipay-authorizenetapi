<?php

namespace Omnipay\AuthorizeNetApi\Message;

/**
 *
 */

use Academe\AuthorizeNet\Request\Transaction\AuthOnly;
use Academe\AuthorizeNet\Amount\MoneyPhp;
use Academe\AuthorizeNet\Request\Model\NameAddress;
use Academe\AuthorizeNet\Payment\CreditCard;
use Academe\AuthorizeNet\Request\Model\Customer;
use Academe\AuthorizeNet\Request\Model\Retail;
use Academe\AuthorizeNet\AmountInterface;
use Academe\AuthorizeNet\Payment\Track1;
use Academe\AuthorizeNet\Payment\Track2;
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

        $transaction = $this->createTransaction($amount);

        if ($card = $this->getCard()) {
            $billTo = new NameAddress(
                $card->getBillingFirstName(),
                $card->getBillingLastName(),
                $card->getBillingCompany(),
                trim($card->getBillingAddress1() . ' ' . $card->getBillingAddress2()),
                $card->getBillingCity(),
                $card->getBillingState(),
                $card->getBillingPostcode(),
                $card->getBillingCountry()
            );

            // The billTo may have phone and fax number, but the shipTo does not.
            $billTo = $billTo->withPhoneNumber($card->getBillingPhone());
            $billTo = $billTo->withFaxNumber($card->getBillingFax());

            if ($billTo->hasAny()) {
                $transaction = $transaction->withBillTo($billTo);
            }

            $shipTo = new NameAddress(
                $card->getShippingFirstName(),
                $card->getShippingLastName(),
                $card->getShippingCompany(),
                trim($card->getShippingAddress1() . ' ' . $card->getShippingAddress2()),
                $card->getShippingCity(),
                $card->getShippingState(),
                $card->getShippingPostcode(),
                $card->getShippingCountry()
            );

            if ($shipTo->hasAny()) {
                $transaction = $transaction->withShipTo($shipTo);
            }

            if ($card->getEmail()) {
                // TODO: customer type may be Customer::CUSTOMER_TYPE_INDIVIDUAL or
                // Customer::CUSTOMER_TYPE_BUSINESS and it would be nice to be able
                // to set it.

                $customer = new Customer();
                $customer = $customer->withEmail($card->getEmail());
                $transaction = $transaction->withCustomer($customer);
            }

            // Credit card, track 1 and track 2 are mutually exclusive.

            // A credit card has been supplied.
            if ($card->getNumber()) {
                $card->validate();

                $creditCard = new CreditCard(
                    $card->getNumber(),
                    // Either MMYY or MMYYYY will work.
                    $card->getExpiryMonth() . $card->getExpiryYear()
                );

                if ($card->getCvv()) {
                    $creditCard = $creditCard->withCardCode($card->getCvv());
                }

                $transaction = $transaction->withPayment($creditCard);
            } elseif ($card->getTrack1()) {
                // A card magnetic track has been supplied (aka card present).

                $transaction = $transaction->withPayment(
                    new Track1($card->getTrack1())
                );
            } elseif ($card->getTrack2()) {
                $transaction = $transaction->withPayment(
                    new Track2($card->getTrack2())
                );
            }
        } // credit card

        if ($this->getClientIp()) {
            $transaction = $transaction->withCustomerIp($this->getClientIp());
        }

        // The MarketType and DeviceType is mandatory if tracks are supplied.
        if ($this->getDeviceType() || $this->getMarketType() || (isset($card) && $card->getTracks())) {
            // TODO: accept optional customerSignature
            $retail = new Retail(
                $this->getMarketType() ?: Retail::MARKET_TYPE_RETAIL,
                $this->getDeviceType() ?: Retail::DEVICE_TYPE_UNKNOWN
            );

            $transaction = $transaction->withRetail($retail);
        }

        $transaction = $transaction->with([
            // ...other data here...
        ]);

        return $transaction;
    }

    /**
     * Create a new instance of the transaction object.
     */
     protected function createTransaction(AmountInterface $amount)
    {
        return new AuthOnly($amount);
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

    /**
     * TODO: validate values is one of Retail::DEVICE_TYPE_*
     * @param int $value The retail device type.
     * @return $this
     */
    public function setDeviceType($value)
    {
        return $this->setParameter('deviceType', $value);
    }

    /**
     * @return int
     */
    public function getDeviceType()
    {
        return $this->getParameter('deviceType');
    }

    /**
     * TODO: validate values is one of Retail::MARKET_TYPE_*
     * @param int $value The retail market type.
     * @return $this
     */
    public function setMarketType($value)
    {
        return $this->setParameter('marketType', $value);
    }

    /**
     * @return int
     */
    public function getMarketType()
    {
        return $this->getParameter('marketType');
    }
}
