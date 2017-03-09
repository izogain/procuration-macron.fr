<?php

namespace EnMarche\Bundle\MailjetBundle\Entity;

use EnMarche\Bundle\MailjetBundle\Message\MailjetMessage;
use AppBundle\ValueObject\SHA1;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MailjetEmail
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var UuidInterface
     */
    private $uuid;

    /**
     * The Mailjet message UUID.
     *
     * @var UuidInterface
     */
    private $messageBatchUuid;

    /**
     * The Mailjet email subject.
     *
     * @var string
     */
    private $subject;

    /**
     * The Mailjet email recipient email address.
     *
     * @var string
     */
    private $recipient;

    /**
     * The Mailjet template ID.
     *
     * @var string
     */
    private $template;

    /**
     * The Mailjet message class namespace.
     *
     * @var string|null
     */
    private $messageClass;

    /**
     * The API request JSON payload.
     *
     * @ORM\Column(type="text")
     */
    private $requestPayload;

    /**
     * The request payload SHA1 checksum.
     *
     * @var string
     */
    private $requestPayloadChecksum;

    /**
     * The successful API response JSON payload.
     *
     * @var string|null
     */
    private $responsePayload;

    /**
     * The response payload SHA1 checksum.
     *
     * @var string|null
     */
    private $responsePayloadChecksum;

    /**
     * Whether or not the message was successfully delivered.
     *
     * @ORM\Column(type="boolean")
     */
    private $delivered;

    /**
     * The date and time when the email was sent.
     *
     * @var \DateTime
     */
    private $sentAt;

    public function __construct(
        UuidInterface $uuid,
        UuidInterface $messageBatchUuid,
        string $template,
        string $subject,
        string $recipient,
        string $requestPayload,
        string $responsePayload = null,
        string $messageClass = null,
        bool $delivered = false,
        string $sentAt = 'now',
        UuidInterface $batch = null
    ) {
        $this->uuid = $uuid;
        $this->messageBatchUuid = $messageBatchUuid;
        $this->template = $template;
        $this->subject = $subject;
        $this->recipient = $recipient;
        $this->setPayloads($requestPayload, $responsePayload);
        $this->messageClass = $messageClass;
        $this->delivered = $delivered;
        $this->sentAt = new \DateTime($sentAt);
    }

    /**
     * @param MailjetMessage $message
     * @param string         $recipientEmailAddress
     * @param string|null    $requestPayload
     *
     * @return MailjetEmail
     */
    public static function createFromMessage(MailjetMessage $message, string $recipientEmailAddress, $requestPayload): self
    {
        $email = new self(
            Uuid::uuid4(),
            $message->getBatch(),
            $message->getTemplate(),
            $message->getSubject(),
            $recipientEmailAddress,
            $requestPayload,
            null,
            get_class($message)
        );

        return $email;
    }

    /**
     * @param string|null $responsePayload
     */
    public function delivered(string $responsePayload = null)
    {
        if ($responsePayload) {
            $this->setResponsePayload($responsePayload);
        }

        $this->delivered = true;
    }

    /**
     * @return UuidInterface
     */
    public function getMessageBatchUuid(): UuidInterface
    {
        return $this->messageBatchUuid;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * Return the message class name with legacy control
     *
     * @return string
     */
    public function getMessageClass()
    {
        if (false === mb_strpos('AppBundle\\Mailjet\\Message\\', $this->messageClass)) {
            return str_replace('EnMarche\\Bundle\\MailjetBundle\\Message', '', $this->messageClass);
        }

        return str_replace('AppBundle\\Mailjet\\Message\\', '', $this->messageClass);
    }

    /**
     * @return string
     */
    public function getRequestPayload(): string
    {
        return $this->requestPayload;
    }

    /**
     * @return null|string
     */
    public function getResponsePayload()
    {
        return $this->responsePayload;
    }

    /**
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->delivered;
    }

    /**
     * @param string      $requestPayload
     * @param string|null $responsePayload
     */
    private function setPayloads(string $requestPayload, string $responsePayload = null)
    {
        $this->requestPayload = $requestPayload;
        $this->requestPayloadChecksum = SHA1::hash($requestPayload)->getHash();

        if ($responsePayload) {
            $this->setResponsePayload($responsePayload);
        }
    }

    /**
     * @param string $responsePayload
     */
    private function setResponsePayload(string $responsePayload)
    {
        $this->responsePayload = $responsePayload;
        $this->responsePayloadChecksum = SHA1::hash($responsePayload)->getHash();
    }
}
