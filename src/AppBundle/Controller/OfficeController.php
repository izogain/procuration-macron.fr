<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Office;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OfficeController extends AbstractController
{
    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('office/index.html.twig', [
            'offices' => $this->getOfficeMediator()->getAllWithCredentials($this->getUser()),
        ]);
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return Response
     */
    public function editAction(Request $request, $id): Response
    {
        $office = $this->getValidOffice($id);
        $formHandler = $this->getOfficeFormHandler();
        $form = $formHandler->createForm($office);

        if ($formHandler->process($form, $request)) {
            return $this->redirectToRoute('office_index');
        }

        return $this->render('office/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param string  $id
     *
     * @return Response
     */
    public function deleteAction($id): Response
    {
        $office = $this->getValidOffice($id);
        $this->deleteEntity($office);

        return $this->redirectToRoute('office_index');
    }

    /**
     * @param int $id
     *
     * @return null|Office
     */
    private function getValidOffice($id)
    {
        if (!$office = $this->getOfficeRepository()->findWithReferents($id)) {
            throw $this->createNotFoundException();
        }

        $currentUser = $this->getUser();

        if (!$currentUser->isSuperAdmin() && !$office->getReferents()->contains($currentUser)) {
            throw $this->createAccessDeniedException();
        }

        return $office;
    }

    /**
     * @return \AppBundle\Repository\OfficeRepository
     */
    private function getOfficeRepository()
    {
        return $this->get('app.repository.office');
    }

    /**
     * @return \AppBundle\Form\Handler\OfficeFormHandler
     */
    private function getOfficeFormHandler()
    {
        return $this->get('app.form.handler.office');
    }
}
