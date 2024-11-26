<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/access-denied")
 */
class AccessDeniedController extends AbstractController
{
    /**
     * @Route("/", name="access_denied")
     */
    public function index()
    {
        return $this->render('security/access_denied.html.twig');
    }
}
