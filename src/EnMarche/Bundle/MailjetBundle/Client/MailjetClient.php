<?php

namespace EnMarche\Bundle\MailjetBundle\Client;

use EnMarche\Bundle\MailjetBundle\Event\MailjetEvent;
use EnMarche\Bundle\MailjetBundle\Event\MailjetEvents;
use EnMarche\Bundle\MailjetBundle\Exception\MailjetException;
use EnMarche\Bundle\MailjetBundle\Factory\MailjetTemplateEmailFactory;
use EnMarche\Bundle\MailjetBundle\Message\MailjetMessage;
use EnMarche\Bundle\MailjetBundle\Transport\MailjetMessageTransportInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MailjetClient
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var MailjetMessageTransportInterface
     */
    private $transport;

    /**
     * @var MailjetTemplateEmailFactory
     */
    private $factory;

    /**
     * @param EventDispatcherInterface         $eventDispatcher
     * @param MailjetMessageTransportInterface $transport
     * @param MailjetTemplateEmailFactory      $factory
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        MailjetMessageTransportInterface $transport,
        MailjetTemplateEmailFactory $factory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->transport = $transport;
        $this->factory = $factory;
    }

    /**
     * @param MailjetMessage $message
     *
     * @return bool
     */
    public function sendMessage(MailjetMessage $message): bool
    {
        $delivered = true;
        $email = $this->factory->createFromMailjetMessage($message);

        $this->eventDispatcher->dispatch(MailjetEvents::DELIVERY_MESSAGE, new MailjetEvent($message, $email));

        try {
            $this->transport->sendTemplateEmail($email);
            $this->eventDispatcher->dispatch(MailjetEvents::DELIVERY_SUCCESS, new MailjetEvent($message, $email));
        } catch (MailjetException $exception) {
            $delivered = false;
            $this->eventDispatcher->dispatch(MailjetEvents::DELIVERY_ERROR, new MailjetEvent($message, $email, $exception));
        }

        return $delivered;
    }
}
