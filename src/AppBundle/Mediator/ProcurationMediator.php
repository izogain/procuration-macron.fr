<?php

namespace AppBundle\Mediator;

use AppBundle\Entity\Procuration;
use AppBundle\Entity\User;
use AppBundle\Repository\ProcurationRepository;
use Doctrine\ORM\EntityManager;

class ProcurationMediator
{
    const REASON_PROFESSIONAL = 0;
    const REASON_HANDICAP = 1;
    const REASON_HEALTH = 2;
    const REASON_REQUIRES_ASSISTANCE = 3;
    const REASON_FORMATION = 4;
    const REASON_HOLIDAYS = 5;
    const REASON_OTHER_LIVING_PLACE = 6;

    /**
     * Get the possible reasons for requesting procuration.
     *
     * @return array
     */
    public static function getReasons()
    {
        return [
            static::REASON_PROFESSIONAL => 'En raison d’obligations professionnelles',
            static::REASON_HANDICAP => 'En raison d’un handicap',
            static::REASON_HEALTH => 'Pour raison de santé',
            static::REASON_REQUIRES_ASSISTANCE => 'En raison d’assistance apportée à une personne malade ou infirme',
            static::REASON_FORMATION => 'En raison d’obligations de formation',
            static::REASON_HOLIDAYS => 'Parce que je suis en vacances',
            static::REASON_OTHER_LIVING_PLACE => 'Parce que je réside dans une commune différente de celle où je suis inscrit(e) sur une liste électorale',
        ];
    }
    /**
     * @var ProcurationRepository
     */
    protected $procurationRepository;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $cerfaOutputRootDir;

    /**
     * @param ProcurationRepository $procurationRepository
     * @param EntityManager         $entityManager
     * @param string                $cerfaOutputRootDir
     */
    public function __construct(
        ProcurationRepository $procurationRepository,
        EntityManager $entityManager,
        $cerfaOutputRootDir
    ) {
        $this->procurationRepository = $procurationRepository;
        $this->entityManager = $entityManager;
        $this->cerfaOutputRootDir = $cerfaOutputRootDir;
    }

    /**
     * @param User $user
     *
     * @return Procuration[]|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getAllWithCredentials(User $user)
    {
        if ($user->isSuperAdmin()) {
            return $this->procurationRepository->findAllWithRelationships();
        }

        return $this->procurationRepository->findByUserArea($user->getId());
    }

    /**
     * @param Procuration $procuration
     *
     * @return bool
     */
    public function isDeletable(Procuration $procuration)
    {
        return null === $procuration->getVotingAvailability();
    }

    /**
     * @param Procuration $procuration
     * @param bool        $withFlush
     */
    public function delete(Procuration $procuration, $withFlush = true)
    {
        $this->entityManager->remove($procuration);

        if ($withFlush) {
            $this->entityManager->flush();
        }

        // TODO send email to $procuration->getRequester()
    }

    /**
     * @param Procuration $procuration
     * @param bool        $withFlush
     */
    public function unbind(Procuration $procuration, $withFlush = true)
    {
        $procuration->setVotingAvailability(null);

        $this->entityManager->persist($procuration);

        if ($withFlush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param Procuration $procuration
     *
     * @return string
     */
    public function generateOutputFilePath(Procuration $procuration)
    {
        $procurationId = $procuration->getId();

        return $this->cerfaOutputRootDir.'/'. ($procurationId%8).'/'.$procurationId.'.pdf';
    }
}
