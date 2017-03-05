<?php

namespace EnMarche\Bundle\MailjetBundle\Event;

use EnMarche\Bundle\MailjetBundle\Exception\MailjetException;
use EnMarche\Bundle\MailjetBundle\Template\MailjetTemplateEmail;
use EnMarche\Bundle\MailjetBundle\Message\MailjetMessage;
use Symfony\Component\EventDispatcher\Event;

class MailjetEvent extends Event
{
    /**
     * @var MailjetMessage
     */
    private $message;

    /**
     * @var MailjetTemplateEmail
     */
    private $email;

    /**
     * @var MailjetException|null
     */
    private $exception;

    /**
     * @param MailjetMessage        $message
     * @param MailjetTemplateEmail  $email
     * @param MailjetException|null $exception
     */
    public function __construct(
        MailjetMessage $message,
        MailjetTemplateEmail $email,
        MailjetException $exception = null
    ) {
        $this->message = $message;
        $this->email = $email;
        $this->exception = $exception;
    }

    /**
     * @return MailjetMessage
     */
    public function getMessage(): MailjetMessage
    {
        return $this->message;
    }

    /**
     * @return MailjetTemplateEmail
     */
    public function getEmail(): MailjetTemplateEmail
    {
        return $this->email;
    }

    /**
     * @return MailjetException|null
     */
    public function getException()
    {
        return $this->exception;
    }
}
