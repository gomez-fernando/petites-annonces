<?php

namespace App\Controller\Admin;

use App\Entity\Annonce;
use App\Entity\Category;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/announces", name="admin_annonces_")
 * @package App\Controller
 */
class AnnonceController extends AbstractController
{
  private AnnonceRepository $annonceRepository;

  private EntityManagerInterface $manager;

  public function __construct(AnnonceRepository $annonceRepository, EntityManagerInterface $manager)
  {
    $this->annonceRepository = $annonceRepository;
    $this->manager = $manager;
  }
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('admin/annonce/index.html.twig', [
            'annonces' => $this->annonceRepository->findAll(),
        ]);
    }

  /**
   * @Route("/activate/{id}", name="activate")
   */
  public function activate(Annonce $annonce): Response
  {
    $annonce->setActive(($annonce->getActive()) ? false : true);

//    $em = $this->getDoctrine()->getManager();
    $this->manager->persist($annonce);
    $this->manager->flush();

    $state = ($annonce->getActive() ? 'active' : 'inactive');

    return new Response($state);

  }

  /**
   * @Route("/delete/{id}", name="delete")
   */
  public function delete(Annonce $annonce): Response
  {
    $this->manager->remove($annonce);
    $this->manager->flush();

    $this->addFlash('success', 'Announce deleted successfully');
    return $this->redirectToRoute('admin_annonces_home');

  }


}
