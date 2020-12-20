<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

  /**
   * @Route("/user/announce/add", name="user_add_annonce")
   */
  public function addAnnonce(Request $request): Response
  {
    $annonce = new Annonce();
    $form = $this->createForm(AnnonceType::class, $annonce);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
      $annonce->setUser($this->getUser());

      $em = $this->getDoctrine()->getManager();
      $em->persist($annonce);
      $em->flush();

      return $this->redirectToRoute('user');
    }

    return $this->render('user/annonces/add.html.twig', [
        'form' => $form->createView()
    ]);
  }
}
