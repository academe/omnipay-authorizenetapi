<?php

namespace Omnipay\AuthorizeNetApi\Message;

/**
 * TODO: validate the server request signature.
 */

use Omnipay\Common\Message\NotificationInterface;
use Omnipay\Common\Http\ClientInterface;
use Omnipay\Common\Message\RequestInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

use Omnipay\AuthorizeNetApi\Traits\HasGatewayParams;
use Academe\AuthorizeNet\ServerRequest\Notification;
use Academe\AuthorizeNet\Response\Model\TransactionResponse;

class AcceptNotification extends AbstractRequest implements NotificationInterface //, RequestInterface
{
    use HasGatewayParams;

    protected $data;

    /**
     * The reponse data parsed into nested value objects.
     */
    protected $parsedData;

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
        if ($this->getEventTarget() === $this->getParsedData()::EVENT_TARGET_PAYMENT) {
            return $this->getPayload()->getTransId();
        }
    }

    /**
     * Was the transaction successful?
     *
     * @return string Transaction status, one of {@see STATUS_COMPLETED}, {@see #STATUS_PENDING},
     * or {@see #STATUS_FAILED}.
     */
    public function getTransactionStatus()
    {
        $responseCode = $this->getResponseCode();

        if ($responseCode === TransactionResponse::RESPONSE_CODE_APPROVED) {
            return static::STATUS_COMPLETED;
        } elseif ($responseCode === TransactionResponse::RESPONSE_CODE_PENDING) {
            return static::STATUS_PENDIND;
        } elseif ($responseCode !== null) {
            return static::STATUS_FAILED;
        }
    }

    /**
     * Response Message
     *
     * @return string A response message from the payment gateway
     */
    public function getMessage()
    {
        // There are actually no messages in the notifications.
        return '';
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

    /**
     * The main target of the notificaiton: payment or customer.
     */
    public function getEventTarget()
    {
        return $this->getParsedData()->getEventTarget();
    }

    /**
     * The sub-target of the notificaiton.
     */
    public function getEventSubtarget()
    {
        return $this->getParsedData()->getEventSubtarget();
    }

    /**
     * The action against the target of the notificaito.
     */
    public function getEventAction()
    {
        return $this->getParsedData()->getEventAction();
    }

    /**
     * The UUID identifying this specific notification.
     */
    public function getNotificationId()
    {
        return $this->getParsedData()->getNotificationId();
    }

    /**
     * The UUID identifying the webhook being fired.
     */
    public function getWebhookId()
    {
        return $this->getParsedData()->getWebhookId();
    }

    /**
     * Optional notification payload.
     */
    public function getPayload()
    {
        return $this->getParsedData()->getPayload();
    }

    /**
     * @return int Raw response code
     */
    public function getResponseCode()
    {
        if ($this->getEventTarget() === $this->getParsedData()::EVENT_TARGET_PAYMENT) {
            return $this->getPayload()->getResponseCode();
        }
    }

    /**
     * @return string Raw response code
     */
    public function getAuthCode()
    {
        if ($this->getEventTarget() === $this->getParsedData()::EVENT_TARGET_PAYMENT) {
            return $this->getPayload()->getAuthCode();
        }
    }

    /**
     * @return string Raw AVS response code
     */
    public function getAvsResponse()
    {
        if ($this->getEventTarget() === $this->getParsedData()::EVENT_TARGET_PAYMENT) {
            return $this->getPayload()->getAvsResponse();
        }
    }

    /**
     * @return float authAmount, no currency, no stated units
     */
    public function getAuthAmount()
    {
        if ($this->getEventTarget() === $this->getParsedData()::EVENT_TARGET_PAYMENT) {
            return $this->getPayload()->getAuthAmount();
        }
    }
}
