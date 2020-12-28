<?php

namespace App\Controller;

use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
  private AnnonceRepository $annonceRepository;

  public function __construct(AnnonceRepository $annonceRepository)
  {
    $this->annonceRepository = $annonceRepository;
  }
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
//      dd($this->getUser()->getRoles());
        return $this->render('main/index.html.twig', [
//            'annonces' => $this->annonceRepository->findBy(['active' => true], ['createdAt' => 'DESC', 'title' => 'DESC'], 1)
             'annonces' => $this->annonceRepository->findBy(['active' => true], ['createdAt' => 'DESC', 'title' => 'DESC'], )

        ]);
    }
}
