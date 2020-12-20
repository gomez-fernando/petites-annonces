<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryType;

/**
 * @Route("/admin", name="admin_")
 * @package App\Controller
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

  /**
   * @Route("/categories/add", name="categories_add")
   */
  public function addCategory(Request $request): Response
  {
    $category = new Category();

    $form = $this->createForm(CategoryType::class, $category);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
      $em = $this->getDoctrine()->getManager();
      $em->persist($category);
      $em->flush();

      return $this->redirectToRoute('admin_home');
    }

    return $this->render('admin/category/add.html.twig', [
        'form' => $form->createView(),
    ]);
  }
}
