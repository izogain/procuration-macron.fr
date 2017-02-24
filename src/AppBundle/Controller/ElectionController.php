<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ElectionController extends AbstractController
{
    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('election/index.html.twig', [
            'elections' => $this->getElectionRepository()->findAllByDateDesc(),
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
            return $this->redirectToRoute('election_index');
        }

        return $this->render('election/new.html.twig', [
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
        if (!$election = $this->getElectionRepository()->find($id)) {
            throw $this->createNotFoundException(sprintf('No election with ID "%s"', $id));
        }

        $formHandler = $this->getElectionFormHandler();
        $form = $formHandler->createForm($election);

        if ($formHandler->process($form, $request)) {
            return $this->redirectToRoute('election_index');
        }

        return $this->render('election/edit.html.twig', [
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
        if (!$election = $this->getElectionRepository()->find($id)) {
            throw $this->createNotFoundException(sprintf('No election with ID "%s"', $id));
        }

        $this->deleteEntity($election);

        return $this->redirectToRoute('election_index');
    }

    /**
     * @return \AppBundle\Repository\ElectionRepository
     */
    private function getElectionRepository()
    {
        return $this->get('app.repository.election');
    }

    /**
     * @return \AppBundle\Form\Handler\ElectionFormHandler
     */
    private function getElectionFormHandler()
    {
        return $this->get('app.form.handler.election');
    }
}
