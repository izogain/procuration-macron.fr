<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ElectionRoundController extends AbstractController
{
    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('election-round/index.html.twig', [
            'election_rounds' => $this->getElectionRoundRepository()->findAllByDateDesc(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request): Response
    {
        $formHandler = $this->getElectionFormHandler();
        $form = $formHandler->createForm();

        if ($formHandler->process($form, $request)) {
            return $this->redirectToRoute('election_round_index');
        }

        return $this->render('election-round/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param int     $id
     *
     * @return Response
     */
    public function editAction(Request $request, $id): Response
    {
        if (!$election = $this->getElectionRoundRepository()->findOneWithRelations($id)) {
            throw $this->createNotFoundException(sprintf('No election round with ID "%s"', $id));
        }

        $formHandler = $this->getElectionFormHandler();
        $form = $formHandler->createForm($election);

        if ($formHandler->process($form, $request)) {
            return $this->redirectToRoute('election_round_index');
        }

        return $this->render('election-round/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function deleteAction($id): Response
    {
        if (!$election = $this->getElectionRoundRepository()->find($id)) {
            throw $this->createNotFoundException(sprintf('No election round with ID "%s"', $id));
        }

        $this->deleteEntity($election);

        return $this->redirectToRoute('election_round_index');
    }

    /**
     * @return \AppBundle\Repository\ElectionRoundRepository
     */
    private function getElectionRoundRepository()
    {
        return $this->get('app.repository.election_round');
    }

    /**
     * @return \AppBundle\Form\Handler\ElectionRoundFormHandler
     */
    private function getElectionFormHandler()
    {
        return $this->get('app.form.handler.election_round');
    }
}
