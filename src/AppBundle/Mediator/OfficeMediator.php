<?php

namespace AppBundle\Mediator;

use AppBundle\Entity\User;
use AppBundle\Repository\OfficeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class OfficeMediator
{
    /**
     * @var OfficeRepository
     */
    protected $officeRepository;

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
     * @param OfficeRepository   $officeRepository
     * @param PaginatorInterface $paginator
     * @param string             $paginationPageParameterName
     * @param int                $paginationSize
     */
    public function __construct(
        OfficeRepository $officeRepository,
        PaginatorInterface $paginator,
        $paginationPageParameterName,
        $paginationSize
    ) {
        $this->officeRepository = $officeRepository;
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
    public function getPaginatedWithCredentials(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            $query = $this->officeRepository->createQueryBuilder('o');
        } else {
            $query = $this->officeRepository->getQueryBuilderAllForReferent($user);
        }

        return $this->paginator->paginate(
            $query,
            $request->query->getInt($this->paginationPageParameterName, 1),
            $this->paginationSize
        );
    }
}
