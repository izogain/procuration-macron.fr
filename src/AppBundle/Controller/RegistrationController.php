<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends AbstractController
{
    /**
     * @param Request $request
     * @param         $hash
     *
     * @return Response
     */
    public function registerAction(Request $request, $hash): Response
    {
        if (!$voterInvitation = $this->getVoterInvitationRepository()->findOneActiveByHash($hash)) {
            throw $this->createNotFoundException();
        }

        $user = $this->getUserRepository()->findOneByEmail($voterInvitation->getEmail());

        $formHandler = $this->getVoterInvitationFormHandler();
        $form = $formHandler->createFormFromVoterInvitation($voterInvitation, $user);

        if ($formHandler->process($form, $request, $voterInvitation)) {
            return $this->redirectToRoute('registration_confirmation');
        }

        return $this->render('registration/index.html.twig', [
            'form' => $form->createView(),
            'hash' => $hash,
        ]);
    }

    /**
     * @return Response
     */
    public function confirmationAction(): Response
    {
        return $this->render('registration/confirmation.html.twig');
    }

    /**
     * @return \AppBundle\Repository\VoterInvitationRepository
     */
    private function getVoterInvitationRepository()
    {
        return $this->get('app.repository.voter_invitation');
    }

    /**
     * @return \AppBundle\Form\Handler\RegistrationFormHandler
     */
    private function getVoterInvitationFormHandler()
    {
        return $this->get('app.form.handler.registration');
    }
}
