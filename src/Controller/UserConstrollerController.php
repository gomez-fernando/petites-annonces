<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserConstrollerController extends AbstractController
{
    /**
     * @Route("/user/constroller", name="user_constroller")
     */
    public function index(): Response
    {
        return $this->render('user_constroller/index.html.twig', [
            'controller_name' => 'UserConstrollerController',
        ]);
    }
}
