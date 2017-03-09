<?php

namespace EnMarche\Bundle\MailjetBundle\Template;

use EnMarche\Bundle\MailjetBundle\Exception\MailjetException;
use EnMarche\Bundle\MailjetBundle\Message\MailjetMessage;

final class MailjetTemplateEmail implements \JsonSerializable
{
    /**
     * @var string
     */
    private $senderEmail;

    /**
     * @var string
     */
    private $senderName;

    /**
     * @var string
     */
    private $replyTo;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var array
     */
    private $recipients;

    /**
     * @var string
     */
    private $template;

    /**
     * @var string|null
     */
    private $httpRequestPayload;

    /**
     * @var string|null
     */
    private $httpResponsePayload;

    /**
     * @param string      $template
     * @param string      $subject
     * @param string      $senderEmail
     * @param string|null $senderName
     * @param string|null $replyTo
     */
    public function __construct(
        string $template,
        string $subject,
        string $senderEmail,
        string $senderName = null,
        string $replyTo = null
    ) {
        $this->template = $template;
        $this->subject = $subject;
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
        $this->replyTo = $replyTo;
        $this->recipients = [];
    }

    /**
     * @param MailjetMessage $message
     * @param string         $senderEmail
     * @param string|null    $senderName
     *
     * @return MailjetTemplateEmail
     */
    public static function createWithMailjetMessage(MailjetMessage $message, string $senderEmail, string $senderName = null): self
    {
        $email = new self($message->getTemplate(), $message->getSubject(), $senderEmail, $senderName, $message->getReplyTo());

        foreach ($message->getRecipients() as $recipient) {
            $email->addRecipient($recipient->getEmailAddress(), $recipient->getFullName(), $recipient->getVars());
        }

        return $email;
    }

    /**
     * @param string      $email
     * @param string|null $name
     * @param array       $vars
     */
    public function addRecipient(string $email, string $name = null, array $vars = [])
    {
        $recipient['Email'] = $email;

        if ($name) {
            $recipient['Name'] = $name;
        }

        if (count($vars)) {
            $recipient['Vars'] = $vars;
        }

        $this->recipients[] = $recipient;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        if (!count($this->recipients)) {
            throw new MailjetException('The Mailjet email requires at least one recipient.');
        }

        $body['FromEmail'] = $this->senderEmail;
        if ($this->senderName) {
            $body['FromName'] = $this->senderName;
        }

        $body['Subject'] = $this->subject;
        $body['MJ-TemplateID'] = $this->template;
        $body['MJ-TemplateLanguage'] = true;
        $body['Recipients'] = $this->recipients;

        if ($this->replyTo) {
            $body['Headers'] = [
                'Reply-To' => $this->replyTo,
            ];
        }

        return $body;
    }

    /**
     * @param string      $httpResponsePayload
     * @param string|null $httpRequestPayload
     */
    public function delivered(string $httpResponsePayload, string $httpRequestPayload = null)
    {
        if ($httpRequestPayload) {
            $this->httpRequestPayload = $httpRequestPayload;
        }

        $this->httpResponsePayload = $httpResponsePayload;
    }

    /**
     * @return string
     */
    public function getHttpRequestPayload(): string
    {
        if (!$this->httpRequestPayload) {
            $this->httpRequestPayload = json_encode($this->getBody());
        }

        return $this->httpRequestPayload;
    }

    /**
     * @return null|string
     */
    public function getHttpResponsePayload(): ?string
    {
        return $this->httpResponsePayload;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): string
    {
        $body = $this->getBody();

        $this->httpRequestPayload = json_encode($body);

        return $body;
    }
}
