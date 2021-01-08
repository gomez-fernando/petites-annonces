<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceContactType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
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

//  PAGINACION
  /**
   * @Route("/", name="list")
   * @param Request $request
   */
  public function index(Request $request)
  {
    $annonces = $this->annonceRepository->findAll();
    // se puede poner por defecto 1 si no hay valor para page
    // /announces/?page=123
    // definimos el número de elementos por página
    $limit = 1;
    // obtenemos el número de página
    $page = (int) $request->query->get('page', 1);

    // obtenemos los anuncios de la página
    $annonces = $this->annonceRepository->getPaginatedAnnounces($page, $limit);
//    dd($page);

    // obtenemos la cantidad total de anuncios
    $total = $this->annonceRepository->getTotalAnnounces();
//    dd($total);
    return $this->render('annonce/index.html.twig', compact(
        'annonces', 'total' , 'limit', 'page'
    ));
  }

  /**
   * @Route("/details/{slug}", name="details")
   * @param Request $request
   * @param $slug
   * @param MailerInterface $mailer
   * @return Response
   */
    public function details(Request $request, $slug, MailerInterface $mailer): Response
    {
      $annonce = $this->annonceRepository->findOneBy(['slug' => $slug]);

      if(!$annonce){
        throw new NotFoundHttpException("This announce doesn't exist");
      }

      $form = $this->createForm(AnnonceContactType::class );
      $contact = $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()){
        $email = (new TemplatedEmail())
            ->from($contact->get('email')->getData())
            ->to($annonce->getUser()->getEmail())
            ->subject('This is a contact about your announce: "' . $annonce->getTitle() . '".')
            ->htmlTemplate('emails/contact_annonce.html.twig')
            ->context([
                'annonce' => $annonce,
              'e_email' => $contact->get('email')->getData(),
              'message' => $contact->get('message')->getData()
            ]);
        $mailer->send($email);

        $this->addFlash('success', 'Your email has been sent');
        return $this->redirectToRoute('annonces_details', ['slug' => $annonce->getSlug() ]);
      }

      return $this->render('annonce/details.html.twig',[
          'annonce' => $annonce,
        'form' => $form->createView()
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
