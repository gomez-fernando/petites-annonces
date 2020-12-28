<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/announces", name="annonces_")
 * Class AnnonceController
 * @package App\Controller
 */
class AnnonceController extends AbstractController
{
  private EntityManagerInterface $em;
  private AnnonceRepository $annonceRepository;

  public function __construct(AnnonceRepository $annonceRepository,
                              EntityManagerInterface $em)
  {
    $this->em = $em;
    $this->annonceRepository = $annonceRepository;
  }
    /**
     * @Route("/details/{slug}", name="details")
     */
    public function details($slug): Response
    {
      $annonce = $this->annonceRepository->findOneBy(['slug' => $slug]);

      if(!$annonce){
        throw new NotFoundHttpException("This announce doesn't exist");
      }
//dd($annonce);
        return $this->render('annonce/details.html.twig',[
            'annonce' => $annonce
        ]);
    }

  /**
   * @Route("/favorites/add/{id}", name="add_favorite")
   */
  public function addFavorite(Annonce $annonce)
  {
    if(!$annonce){
      throw new NotFoundHttpException("This announce doesn't exist");
    }
    $annonce->addFavorite($this->getUser());
    $this->em->persist($annonce);
    $this->em->flush();

    return $this->redirectToRoute('app_home');
  }

  /**
   * @Route("/favorites/remove/{id}", name="remove_favorite")
   */
  public function removeFavorite(Annonce $annonce)
  {
    if(!$annonce){
      throw new NotFoundHttpException("This announce doesn't exist");
    }
    $annonce->removeFavorite($this->getUser());
    $this->em->persist($annonce);
    $this->em->flush();

    return $this->redirectToRoute('app_home');
  }
}
