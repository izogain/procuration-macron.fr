<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        return $this->render('user/index.html.twig', [
            'pagination' => $this->getUserMediator()->getPaginatorFromCredentials($request, $this->getUser()),
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
        if (!$user = $this->getUserRepository()->findOneWithRelations($id)) {
            throw $this->createNotFoundException();
        }

        $formHandler = $this->getUserFormHandler();
        $form = $formHandler->createForm($this->getUser(), $user);

        if ($formHandler->process($form, $request)) {
            $this->addFlash('success', 'Profile modifié avec succès');

            return $this->redirectToRoute('user_edit', ['id' => $id]);
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function newAction(Request $request): Response
    {
        $formHandler = $this->getUserFormHandler();
        $form = $formHandler->createForm($this->getUser());

        if ($formHandler->process($form, $request)) {
            return $this->redirectToRoute('user_edit', ['id' => $form->getData()->getId()]);
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return \AppBundle\Form\Handler\UserFormHandler
     */
    private function getUserFormHandler()
    {
        return $this->get('app.form.handler.user');
    }

    /**
     * @return \AppBundle\Mediator\UserMediator
     */
    private function getUserMediator()
    {
        return $this->get('app.mediator.user');
    }
}
