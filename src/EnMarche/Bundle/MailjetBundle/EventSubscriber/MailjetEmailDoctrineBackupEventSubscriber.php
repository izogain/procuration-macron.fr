<?php

namespace EnMarche\Bundle\MailjetBundle\EventSubscriber;

use EnMarche\Bundle\MailjetBundle\Entity\MailjetEmail;
use EnMarche\Bundle\MailjetBundle\Event\MailjetEvent;
use EnMarche\Bundle\MailjetBundle\Event\MailjetEvents;
use EnMarche\Bundle\MailjetBundle\MailjetUtils;
use EnMarche\Bundle\MailjetBundle\Repository\MailjetEmailRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MailjetEmailDoctrineBackupEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var MailjetEmailRepository
     */
    private $repository;

    /**
     * @param ObjectManager          $manager
     * @param MailjetEmailRepository $repository
     */
    public function __construct(ObjectManager $manager, MailjetEmailRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            MailjetEvents::DELIVERY_MESSAGE => 'onMailjetDeliveryMessage',
            MailjetEvents::DELIVERY_SUCCESS => 'onMailjetDeliverySuccess',
        ];
    }

    /**
     * @param MailjetEvent $event
     */
    public function onMailjetDeliveryMessage(MailjetEvent $event)
    {
        $email = $event->getEmail();
        $message = $event->getMessage();

        foreach ($message->getRecipients() as $recipient) {
            $this->manager->persist(MailjetEmail::createFromMessage(
                $message,
                $recipient->getEmailAddress(),
                $email->getHttpRequestPayload()
            ));
        }

        $this->manager->flush();
    }

    /**
     * @param MailjetEvent $event
     */
    public function onMailjetDeliverySuccess(MailjetEvent $event)
    {
        $templateEmail = $event->getEmail();

        if (!$responsePayload = $templateEmail->getHttpResponsePayload()) {
            return;
        }

        $message = $event->getMessage();
        if (empty($emails = $this->repository->findByMessageBatchUuid($message->getBatch()))) {
            return;
        }

        $recipients = MailjetUtils::getSuccessfulRecipientsFromJson($responsePayload, true);

        foreach ($emails as $email) {
            $recipient = MailjetUtils::canonicalize($email->getRecipient());
            if (isset($recipients[$recipient])) {
                $email->delivered($responsePayload);
            }
        }

        $this->manager->flush();
    }
}
