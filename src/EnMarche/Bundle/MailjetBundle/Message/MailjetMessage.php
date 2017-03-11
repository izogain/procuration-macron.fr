<?php

namespace EnMarche\Bundle\MailjetBundle\Message;

use EnMarche\Bundle\MailjetBundle\Recipient\MailjetMessageRecipient;
use Ramsey\Uuid\UuidInterface;

abstract class MailjetMessage
{
    /**
     * @var UuidInterface
     */
    private $uuid;

    /**
     * @var UuidInterface
     */
    private $batch;

    /**
     * @var array
     */
    private $vars;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $template;

    /**
     * @var array
     */
    private $recipients;

    /**
     * @var string
     */
    private $replyTo;

    /**
     * @param UuidInterface $uuid           The unique identifier of this message
     * @param string        $template       The Mailjet template ID
     * @param string        $recipientEmail The first recipient email address
     * @param string|null   $recipientName  The first recipient name
     * @param string        $subject        The message subject
     * @param array         $commonVars     The common variables shared by all recipients
     * @param array         $recipientVars  The recipient's specific variables
     * @param string        $replyTo        The email address to use for the Reply-to header
     * @param UuidInterface $batch
     */
    final public function __construct(
        UuidInterface $uuid,
        string $template,
        string $recipientEmail,
        $recipientName,
        string $subject,
        array $commonVars = [],
        array $recipientVars = [],
        string $replyTo = null,
        UuidInterface $batch = null
    ) {
        $this->uuid = $uuid;
        $this->recipients = [];
        $this->template = $template;
        $this->subject = $subject;
        $this->vars = $commonVars;
        $this->replyTo = $replyTo;
        $this->batch = $batch ?? $uuid;

        $this->addRecipient($recipientEmail, $recipientName, $recipientVars);
    }

    /**
     * Sets a common shared variable.
     *
     * @param string $name  The variable name
     * @param string $value The variable value
     */
    protected function setVar(string $name, $value)
    {
        $this->vars[$name] = (string) $value;
    }

    /**
     * @return UuidInterface
     */
    final public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @return UuidInterface
     */
    final public function getBatch(): UuidInterface
    {
        return $this->batch;
    }

    /**
     * Returns the common variables shared by all recipients.
     *
     * @return array
     */
    final public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * @return string
     */
    final public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    final public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return null|string
     */
    public function getReplyTo(): ?string
    {
        return $this->replyTo;
    }

    /**
     * @param string      $recipientEmail
     * @param string|null $recipientName
     * @param array       $vars
     */
    final public function addRecipient(string $recipientEmail, $recipientName, array $vars = [])
    {
        $key = mb_strtolower($recipientEmail);
        $vars = array_merge($this->vars, $vars);

        $this->recipients[$key] = new MailjetMessageRecipient($recipientEmail, $recipientName, $vars);
    }

    /**
     * Returns the list of MailjetMessageRecipient instances.
     *
     * @return MailjetMessageRecipient[]
     */
    final public function getRecipients(): array
    {
        return array_values($this->recipients);
    }

    /**
     * @param string|int $key
     *
     * @return MailjetMessageRecipient|null
     */
    final public function getRecipient($key): ?MailjetMessageRecipient
    {
        if (!is_int($key) && !is_string($key)) {
            throw new \InvalidArgumentException('Recipient key must be an integer index or valid email address string.');
        }

        if (is_string($key) && array_key_exists($key = mb_strtolower($key), $this->recipients)) {
            return $this->recipients[$key];
        }

        $recipients = $this->getRecipients();

        return $recipients[$key] ?? null;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    final protected static function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8', false);
    }
}
