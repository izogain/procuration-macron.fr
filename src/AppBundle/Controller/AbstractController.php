<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractController extends Controller
{
    /**
     * @param mixed $entity Any handled entity
     * @param bool $withFlush
     */
    protected function deleteEntity($entity, $withFlush = true)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $entityManager->remove($entity);

        if ($withFlush) {
            $entityManager->flush();
        }
    }

    /**
     * @return \AppBundle\Form\Handler\ProcurationAssignationFormHandler
     */
    protected function getProcurationAssignationFormHandler()
    {
        return $this->get('app.form.handler.procuration_assignation');
    }

    /**
     * @return \AppBundle\Mediator\VotingAvailabilityMediator
     */
    protected function getVotingAvailabilityMediator()
    {
        return $this->get('app.mediator.voting_availability');
    }
}
