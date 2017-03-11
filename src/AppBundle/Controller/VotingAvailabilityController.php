<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VotingAvailabilityController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        return $this->render('voter/index.html.twig', [
            'pagination' => $this->getVotingAvailabilityMediator()->getPaginatedActiveWithCredentials($request, $this->getUser()),
        ]);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function deleteAction($id): Response
    {
        if (!$votingAvailability = $this->getVotingAvailabilityRepository()->find($id)) {
            throw $this->createNotFoundException(sprintf('No voting availability with ID "%s"', $id));
        }

        $this->getVotingAvailabilityMediator()->delete($votingAvailability);

        return new Response('', Response::HTTP_ACCEPTED);
    }

    /**
     * @return \AppBundle\Repository\VotingAvailabilityRepository
     */
    private function getVotingAvailabilityRepository()
    {
        return $this->get('app.repository.voting_availability');
    }
}
