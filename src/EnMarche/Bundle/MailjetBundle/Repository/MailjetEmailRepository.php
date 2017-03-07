<?php

namespace EnMarche\Bundle\MailjetBundle\Repository;

use EnMarche\Bundle\MailjetBundle\Entity\MailjetEmail;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\Uuid;

class MailjetEmailRepository extends EntityRepository
{
    /**
     * Finds a MailjetEmail instance by its UUID.
     *
     * @param string $uuid
     *
     * @return MailjetEmail|null
     */
    public function findOneByUuid(string $uuid): ?MailjetEmail
    {
        return $this->findOneBy(['uuid' => Uuid::fromString($uuid)->toString()]);
    }

    /**
     * Finds a list of MailjetEmail instances having the same message batch UUID.
     *
     * @param string $uuid
     *
     * @return MailjetEmail[]
     */
    public function findByMessageBatchUuid(string $uuid): array
    {
        return $this->findBy(['messageBatchUuid' => Uuid::fromString($uuid)->toString()]);
    }

    /**
     * @param string $messageClass
     * @param string $recipient
     *
     * @return MailjetEmail[]|ArrayCollection
     */
    public function findRecipientMessages(string $messageClass, string $recipient): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.messageClass = :class')
            ->andWhere('e.recipient = :recipient')
            ->orderBy('e.sentAt', 'DESC')
            ->setParameter('class', $messageClass)
            ->setParameter('recipient', $recipient)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string      $messageClass
     * @param string|null $batch
     *
     * @return MailjetEmail[]|ArrayCollection
     */
    public function findMessages(string $messageClass, string $batch = null): array
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->where('e.messageClass = :class')
            ->orderBy('e.sentAt', 'DESC')
            ->setParameter('class', $messageClass);

        if ($batch) {
            $qb->andWhere('e.messageBatchUuid = :batch')
                ->setParameter('batch', $batch);
        }

        return $qb->getQuery()->getResult();
    }
}
