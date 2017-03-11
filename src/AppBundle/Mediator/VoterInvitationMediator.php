<?php

namespace AppBundle\Mediator;

use AppBundle\Entity\User;
use AppBundle\Entity\VoterInvitation;
use AppBundle\Repository\VoterInvitationRepository;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class VoterInvitationMediator
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var VoterInvitationRepository
     */
    protected $voterInvitationRepository;

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
     * @param EntityManager             $entityManager
     * @param VoterInvitationRepository $voterInvitationRepository
     * @param PaginatorInterface        $paginator
     * @param string                    $paginationPageParameterName
     * @param int                       $paginationSize
     */
    public function __construct(
        EntityManager $entityManager,
        VoterInvitationRepository $voterInvitationRepository,
        PaginatorInterface $paginator,
        $paginationPageParameterName,
        $paginationSize
    ) {
        $this->entityManager = $entityManager;
        $this->voterInvitationRepository = $voterInvitationRepository;
        $this->paginator = $paginator;
        $this->paginationPageParameterName = $paginationPageParameterName;
        $this->paginationSize = $paginationSize;
    }

    /**
     * @param VoterInvitation $voterInvitation
     * @param bool            $flush
     */
    public function consume(VoterInvitation $voterInvitation, $flush = true)
    {
        $voterInvitation->setActive(false);
        $this->entityManager->persist($voterInvitation);

        if ($flush) {
            $this->entityManager->flush();
        }
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
            $query = $this->voterInvitationRepository->queryBuilderAllByName();
        } else {
            $query = $this->voterInvitationRepository->queryBuilderSentBy($user->getId());
        }

        return $this->paginator->paginate(
            $query,
            $request->query->getInt($this->paginationPageParameterName, 1),
            $this->paginationSize
        );
    }
}
