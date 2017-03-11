<?php

namespace AppBundle\Mediator;

use AppBundle\Entity\Procuration;
use AppBundle\Entity\User;
use AppBundle\Message\ProcurationUnbindingMessage;
use AppBundle\Repository\ProcurationRepository;
use Doctrine\ORM\EntityManager;
use EnMarche\Bundle\MailjetBundle\Client\MailjetClient;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

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
     * @var MailjetClient
     */
    protected $mailjetClient;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var string
     */
    protected $paginationPageParameterName;

    /**
     * @var int
     */
    protected $paginationSize;

    /**
     * @param ProcurationRepository $procurationRepository
     * @param EntityManager         $entityManager
     * @param string                $cerfaOutputRootDir
     * @param MailjetClient         $mailjetClient
     * @param PaginatorInterface    $paginator
     * @param string                $paginationPageParameterName
     * @param int                   $paginationSize
     */
    public function __construct(
        ProcurationRepository $procurationRepository,
        EntityManager $entityManager,
        $cerfaOutputRootDir,
        MailjetClient $mailjetClient,
        PaginatorInterface $paginator,
        $paginationPageParameterName,
        $paginationSize
    ) {
        $this->procurationRepository = $procurationRepository;
        $this->entityManager = $entityManager;
        $this->cerfaOutputRootDir = $cerfaOutputRootDir;
        $this->mailjetClient = $mailjetClient;
        $this->paginator = $paginator;
        $this->paginationPageParameterName = $paginationPageParameterName;
        $this->paginationSize = $paginationSize;
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Knp\Component\Pager\Pagination\AbstractPagination
     */
    public function getPaginatedFromCredentials(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            $query = $this->procurationRepository->findAllWithRelationships();
        } else {
            $query = $this->procurationRepository->findByUserArea($user->getId());
        }

        return $this->paginator->paginate(
            $query,
            $request->query->getInt($this->paginationPageParameterName, 1),
            $this->paginationSize
        );
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
    }

    /**
     * @param Procuration $procuration
     * @param bool        $withFlush
     */
    public function unbind(Procuration $procuration, $withFlush = true)
    {
        $message = ProcurationUnbindingMessage::createFromModel($procuration);
        $procuration->setVotingAvailability(null);

        $this->entityManager->persist($procuration);

        if ($withFlush) {
            $this->entityManager->flush();
        }

        $this->mailjetClient->sendMessage($message);
    }

    /**
     * @param Procuration $procuration
     *
     * @return string
     */
    public function generateOutputFilePath(Procuration $procuration)
    {
        $procurationId = $procuration->getId();

        return $this->cerfaOutputRootDir.'/'.($procurationId % 8).'/'.$procurationId.'.pdf';
    }
}
