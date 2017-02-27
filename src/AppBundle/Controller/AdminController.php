<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class AdminController extends AbstractController
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('admin/index.html.twig');
    }
}
