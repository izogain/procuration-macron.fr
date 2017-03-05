<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VoterInvitationController extends AbstractController
{
    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('voter-invitation/index.html.twig', [
            'voter_invitations' => $this->getVoterInvitationRepository()->findAllByLastName()
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request): Response
    {
        $formHandler = $this->getVoterInvitationFormHandler();
        $form = $formHandler->createForm($this->getUser());

        if ($formHandler->process($form, $request)) {
            return $this->redirectToRoute('voter-invitation_index');
        }

        return $this->render('voter-invitation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return \AppBundle\Form\Handler\VoterInvitationFormHandler
     */
    private function getVoterInvitationFormHandler()
    {
        return $this->get('app.form.handler.voter_invitation');
    }

    /**
     * @return \AppBundle\Repository\VoterInvitationRepository
     */
    private function getVoterInvitationRepository()
    {
        return $this->get('app.repository.voter_invitation');
    }
}
