<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Office;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @return Response
     */
    public function searchOfficeAction(): Response
    {
        return $this->render('default/_search_office_form_compact.html.twig', [
            'form' => $this->getSubscriptionSearchOfficeFormHandler()->createForm()->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function officeResultsAction(Request $request): JsonResponse
    {
        $term = $request->query->get('term');

        // TODO take office city into consideration
        return new JsonResponse(array_map(function (Office $office) {
            return [
                'id' => $office->getId(),
                'name' => $office->getName(),
            ];
        }, $this->get('app.repository.office')->findAll()));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function votingOfficeAction(Request $request): Response
    {
        $this->get('app.form.handler.subscription')->resetStoredData($request->getSession());
        $formHandler = $this->getSubscriptionSearchOfficeFormHandler();
        $form = $formHandler->createForm();

        if ($formHandler->process($form, $request)) {
            return $this->redirectToRoute('subscribe_my_address');
        }

        return $this->render('default/voting_office.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function myAddressAction(Request $request): Response
    {
        $formHandler = $this->get('app.form.handler.subscription.address');
        $form = $formHandler->createForm();

        if ($formHandler->process($form, $request)) {
            return $this->redirectToRoute('subscribe_elections');
        }

        return $this->render('default/my_address.html.twig', [
            'pagination_step' => 2,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function electionsAction(Request $request): Response
    {
        $formHandler = $this->get('app.form.handler.subscription.election');
        $form = $formHandler->createForm();

        if ($formHandler->process($form, $request)) {
            return $this->redirectToRoute('subscribe_contact_informations');
        }

        return $this->render('default/elections.html.twig', [
            'pagination_step' => 3,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function contactInformationsAction(Request $request): Response
    {
        $formHandler = $this->get('app.form.handler.subscription.contact_informations');
        $form = $formHandler->createForm();

        if ($formHandler->process($form, $request)) {
            return $this->redirectToRoute('subscribe_reason');
        }

        return $this->render('default/contact_informations.html.twig', [
            'pagination_step' => 4,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function reasonAction(Request $request): Response
    {
        $formHandler = $this->get('app.form.handler.subscription.reason');
        $form = $formHandler->createForm();

        if ($formHandler->process($form, $request)) {
            return $this->redirectToRoute('subscribe_confirmation');
        }

        return $this->render('default/reason.html.twig', [
            'pagination_step' => 5,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function confirmationAction(Request $request): Response
    {
        if (!$request->getSession()->getFlashBag()->has('firstName')) {
            throw $this->createNotFoundException();
        }

        return $this->render('default/confirmation.html.twig');
    }

    /**
     * @return \AppBundle\Form\Handler\Subscription\SubscriptionSearchOfficeHandler
     */
    private function getSubscriptionSearchOfficeFormHandler()
    {
        return $this->get('app.form.handler.subscription.search_office');
    }
}
