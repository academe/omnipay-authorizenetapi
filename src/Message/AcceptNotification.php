<?php

namespace Omnipay\AuthorizeNetApi\Message;

/**
 *
 */

use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\Common\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

use Omnipay\AuthorizeNetApi\Traits\HasGatewayParams;
use Academe\AuthorizeNet\ServerRequest\Notification;

class AcceptNotification extends AbstractRequest implements NotificationInterface //, RequestInterface
{
    use HasGatewayParams;

    protected $data;

    /**
     * The reponse data parsed into nested value objects.
     */
    protected $parsedData;

    protected $notification;

    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest)
    {
        // The request is a \Symfony/Component/HttpFoundation/Request object
        // and not (yet) a PSR-7 message.

        if ($httpRequest->getContentType() === 'json') {
            $body = (string)$httpRequest->getContent();
        } else {
            $body = '{}';
        }

        $this->data = json_decode($body, true);

        $this->parsedData = new Notification($this->data);
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
     * Get the raw data array for this message.
     * The raw data will be passed in the body as JSON.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Gateway Reference
     *
     * @return string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        // TODO.
    }

    /**
     * Was the transaction successful?
     *
     * @return string Transaction status, one of {@see STATUS_COMPLETED}, {@see #STATUS_PENDING},
     * or {@see #STATUS_FAILED}.
     */
    public function getTransactionStatus()
    {
        // TODO.
    }

    /**
     * Response Message
     *
     * @return string A response message from the payment gateway
     */
    public function getMessage()
    {
        // TODO.
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
