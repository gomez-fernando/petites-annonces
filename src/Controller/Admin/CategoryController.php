<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryType;

/**
 * @Route("/admin/categories", name="admin_categories_")
 * @package App\Controller
 */
class CategoryController extends AbstractController
{
  private CategoryRepository $categoryRepository;

  public function __construct(CategoryRepository $categoryRepository)
  {
    $this->categoryRepository = $categoryRepository;
  }
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
        ]);
    }

  /**
   * @Route("/add", name="add")
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

      return $this->redirectToRoute('admin_categories_home');
    }

    return $this->render('admin/category/add.html.twig', [
        'form' => $form->createView(),
    ]);
  }

  /**
   * @Route("/edit/{id}", name="edit")
   */
  public function categoryEdit(Request $request, Category $category): Response
  {
    $form = $this->createForm(CategoryType::class, $category);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
      $em = $this->getDoctrine()->getManager();
      $em->persist($category);
      $em->flush();

      return $this->redirectToRoute('admin_categories_home');
    }

    return $this->render('admin/category/add.html.twig', [
        'form' => $form->createView(),
    ]);
  }
}
