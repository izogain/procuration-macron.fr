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
}
