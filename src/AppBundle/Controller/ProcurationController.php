<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Procuration;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProcurationController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        return $this->render('procuration/index.html.twig', [
            'pagination' => $this->getProcurationMediator()->getPaginatedFromCredentials($request, $this->getUser()),
        ]);
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return Response
     */
    public function assignAction(Request $request, $id): Response
    {
        $procuration = $this->getValidProcuration($id);
        $formHandler = $this->getProcurationAssignationFormHandler();
        $form = $formHandler->createForm($procuration);

        if (!$formHandler->process($form, $request)) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        return $this->redirectToRoute('procuration_index');
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function deleteAction($id): Response
    {
        $procuration = $this->getValidProcuration($id);
        $procurationMediator = $this->getProcurationMediator();

        if ($procurationMediator->isDeletable($procuration)) {
            $procurationMediator->delete($procuration);
            $this->deleteProcurationFile($procuration);

            return new Response('', Response::HTTP_ACCEPTED);
        }

        return new Response(sprintf('Procuration %s is not deletable', $id), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param int $id
     *
     * @return BinaryFileResponse
     */
    public function downloadAction($id): BinaryFileResponse
    {
        $procuration = $this->getValidProcuration($id);
        $filePath = $this->getProcurationMediator()->generateOutputFilePath($procuration);

        try {
            $response = new BinaryFileResponse($filePath);
        } catch (FileException $exception) {
            // File does not exist, we regenerate it on the fly
            $this->get('app.fpdi.writer')->generateForProcuration($procuration);
            $response = new BinaryFileResponse($filePath);
        }

        $response->setPrivate();

        return $response;
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    public function unbindAction($id): Response
    {
        $procuration = $this->getValidProcuration($id);
        $this->getProcurationMediator()->unbind($procuration);
        $this->deleteProcurationFile($procuration);

        return $this->redirectToRoute('procuration_index');
    }

    /**
     * @param int $id
     *
     * @return \AppBundle\Entity\Procuration|null
     */
    private function getValidProcuration($id)
    {
        /** @var \AppBundle\Entity\Procuration|null $procuration */
        if (!$procuration = $this->getProcurationRepository()->find($id)) {
            throw $this->createNotFoundException(sprintf('No procuration with ID "%s"', $id));
        }

        return $procuration;
    }

    /**
     * @return \AppBundle\Mediator\ProcurationMediator
     */
    private function getProcurationMediator()
    {
        return $this->get('app.mediator.procuration');
    }

    /**
     * @return \AppBundle\Repository\ProcurationRepository
     */
    private function getProcurationRepository()
    {
        return $this->get('app.repository.procuration');
    }

    /**
     * @param Procuration $procuration
     */
    private function deleteProcurationFile(Procuration $procuration)
    {
        try {
            $this->get('filesystem')->remove([
                $this->getProcurationMediator()->generateOutputFilePath($procuration)
            ]);
        } catch (IOException $e) {
            // Silently fail
        }
    }
}
