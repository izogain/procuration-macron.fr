<?php

namespace AppBundle\Mediator;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class UserMediator
{
    const CIVILITY_MADAM = 0;
    const CIVILITY_MISTER = 1;

    /**
     * @var UserRepository
     */
    protected $repository;

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
     * @param UserRepository     $repository
     * @param PaginatorInterface $paginator
     * @param string             $paginationPageParameterName
     * @param int                $paginationSize
     */
    public function __construct(
        UserRepository $repository,
        PaginatorInterface $paginator,
        $paginationPageParameterName,
        $paginationSize
    ) {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->paginationPageParameterName = $paginationPageParameterName;
        $this->paginationSize = $paginationSize;
    }

    /**
     * @return array
     */
    public static function getCivilities()
    {
        return [
            static::CIVILITY_MADAM => 'Mme',
            static::CIVILITY_MISTER => 'M',
        ];
    }

    /**
     * @param int $value
     *
     * @return string
     */
    public function getCivility($value)
    {
        $civilities = static::getCivilities();

        return $civilities[$value] ?? '';
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Knp\Component\Pager\Pagination\AbstractPagination
     */
    public function getPaginatorFromCredentials(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            $queryBuilder = $this->repository->getQueryBuilderAllWithRelationshipsByName();
        } else {
            $queryBuilder = $this->repository->getQueryBuilderAllForReferent($user);
        }

        return $this->paginator->paginate(
            $queryBuilder,
            $request->query->getInt($this->paginationPageParameterName, 1),
            $this->paginationSize
        );
    }
}
