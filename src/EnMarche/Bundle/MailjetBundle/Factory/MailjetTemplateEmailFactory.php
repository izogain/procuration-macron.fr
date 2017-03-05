<?php

namespace EnMarche\Bundle\MailjetBundle\Factory;

use EnMarche\Bundle\MailjetBundle\Message\MailjetMessage;
use EnMarche\Bundle\MailjetBundle\Template\MailjetTemplateEmail;

class MailjetTemplateEmailFactory
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
     * @param string $senderEmail
     * @param string $senderName
     */
    public function __construct(string $senderEmail, string $senderName)
    {
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
    }

    /**
     * @param MailjetMessage $message
     *
     * @return MailjetTemplateEmail
     */
    public function createFromMailjetMessage(MailjetMessage $message)
    {
        return MailjetTemplateEmail::createWithMailjetMessage($message, $this->senderEmail, $this->senderName);
    }
}
