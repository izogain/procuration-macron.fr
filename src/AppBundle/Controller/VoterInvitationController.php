<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class VoterInvitationController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        return $this->render('voter-invitation/index.html.twig', [
            'pagination' => $this->getVoterInvitationMediator()->getPaginatedFromCredentials($request, $this->getUser()),
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
            return $this->redirectToRoute('voter_invitation_index');
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
     * @return \AppBundle\Mediator\VoterInvitationMediator
     */
    private function getVoterInvitationMediator()
    {
        return $this->get('app.mediator.voter_invitation');
    }
}
