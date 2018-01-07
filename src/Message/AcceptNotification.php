<?php

namespace Omnipay\AuthorizeNetApi\Message;

/**
 *
 */

use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Http\Client;
use Omnipay\Common\Message\AbstractRequest as OmnipayAbstractRequest;
use Omnipay\Common\Message\RequestInterface;

use Academe\AuthorizeNet\ServerRequest\Notification;

class AcceptNotification implements NotificationInterface, RequestInterface
{
    /**
     * The reponse data parsed into nested value objects.
     */
    protected $parsedData;

    public function __construct(Client $client, $data)
    {
        // Parse the raw data into a response message value object.
        //$this->setParsedData(new Notification($data));
        var_dump($client);
        echo "<hr />";
        var_dump($data);
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

    // Interface methods.

    // Interface NotificationInterface

    /**
     * Get the raw data array for this message.
     * The raw data will be passed in the body as JSON.
     *
     * @return mixed
     */
    public function getData()
    {
    }

    /**
     * Gateway Reference
     *
     * @return string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
    }

    /**
     * Was the transaction successful?
     *
     * @return string Transaction status, one of {@see STATUS_COMPLETED}, {@see #STATUS_PENDING},
     * or {@see #STATUS_FAILED}.
     */
    public function getTransactionStatus()
    {
    }

    /**
     * Response Message
     *
     * @return string A response message from the payment gateway
     */
    public function getMessage()
    {
    }

    // Interface RequestInterface

    /**
     * Initialize request with parameters
     * @param array $parameters The parameters to send
     */
    public function initialize(array $parameters = [])
    {
    }

    /**
     * Get all request parameters
     *
     * @return array
     */
    public function getParameters()
    {
    }

    /**
     * Get the response to this request (if the request has been sent)
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
    }

    /**
     * Send the request
     *
     * @return ResponseInterface
     */
    public function send()
    {
    }

    /**
     * There is nothing to send in order to response to this webhook.
     * The merchant site just needs to return a HTTP 200.
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        return $this;
    }
}
