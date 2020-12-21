<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Form\RegistrationFormType;
use App\Form\SelfEditUserType;
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
        return $this->render('user/index.html.twig');
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


  /**
   * @Route("/user/edit/profile", name="user_edit_profile")
   */
  public function userEditProfile(Request $request): Response
  {
    $user = $this->getUser();
    $form = $this->createForm(SelfEditUserType::class, $user);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){

      $em = $this->getDoctrine()->getManager();
      $em->persist($user);
      $em->flush();

      $this->addFlash('message', 'Profile updated successfully');
      return $this->redirectToRoute('user');
    }

    return $this->render('user/edit-profile.html.twig', [
        'form' => $form->createView()
    ]);
  }
}
