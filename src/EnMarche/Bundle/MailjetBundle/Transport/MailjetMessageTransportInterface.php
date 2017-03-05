<?php

namespace EnMarche\Bundle\MailjetBundle\Transport;

use EnMarche\Bundle\MailjetBundle\Template\MailjetTemplateEmail;

interface MailjetMessageTransportInterface
{
    /**
     * Delivers the email to the recipients.
     *
     * @param MailjetTemplateEmail $email
     */
    public function sendTemplateEmail(MailjetTemplateEmail $email);
}
