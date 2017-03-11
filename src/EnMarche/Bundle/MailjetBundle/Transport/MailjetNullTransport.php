<?php

namespace EnMarche\Bundle\MailjetBundle\Transport;

use EnMarche\Bundle\MailjetBundle\Template\MailjetTemplateEmail;
use Psr\Log\LoggerInterface;

class MailjetNullTransport implements MailjetMessageTransportInterface
{
    /**
     * @var null|LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function sendTemplateEmail(MailjetTemplateEmail $email)
    {
        if ($this->logger) {
            $this->logger->info('[mailjet] sending email with Mailjet.', [
                'message' => $email->getBody(),
            ]);
        }
    }
}
