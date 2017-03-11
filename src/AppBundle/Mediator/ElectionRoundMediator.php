<?php

namespace AppBundle\Mediator;

use AppBundle\Repository\ElectionRoundRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ElectionRoundMediator
{
    /**
     * @var ElectionRoundRepository
     */
    protected $electionRoundRepository;

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
     * @param ElectionRoundRepository $electionRoundRepository
     * @param PaginatorInterface      $paginator
     * @param string                  $paginationPageParameterName
     * @param int                     $paginationSize
     */
    public function __construct(
        ElectionRoundRepository $electionRoundRepository,
        PaginatorInterface $paginator,
        $paginationPageParameterName,
        $paginationSize
    ) {
        $this->electionRoundRepository = $electionRoundRepository;
        $this->paginator = $paginator;
        $this->paginationPageParameterName = $paginationPageParameterName;
        $this->paginationSize = $paginationSize;
    }

    /**
     * @param Request $request
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    public function getAllPaginated(Request $request)
    {
        return $this->paginator->paginate(
            $this->electionRoundRepository->getQueryBuilderfindAllByDateDesc(),
            $request->query->getInt($this->paginationPageParameterName, 1),
            $this->paginationSize
        );
    }
}
