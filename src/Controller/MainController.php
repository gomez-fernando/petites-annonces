<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Form\SearchAnnonceType;
use App\Repository\AnnonceRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
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
   * @param Request $request
   * @return Response
   */
    public function index(Request $request): Response
    {
//      dd($this->getUser()->getRoles());
        $annonces = $this->annonceRepository->findBy(['active' => true], ['createdAt' => 'DESC', 'title' => 'DESC'], 5);

        $form = $this->createForm(SearchAnnonceType::class);
        $search = $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
          $annonces = $this->annonceRepository->search(
              $search->get('words')->getData(),
              $search->get('category')->getData(),
          );
        }

        return $this->render('main/index.html.twig', [
//            'annonces' => $this->annonceRepository->findBy(['active' => true], ['createdAt' => 'DESC', 'title' => 'DESC'])
             'annonces' => $annonces,
            'form' => $form->createView()

        ]);
    }

  /**
   * @Route("/contact", name="contact")
   * @param Request $request
   * @param MailerInterface $mailer
   * @return Response
   * @throws TransportExceptionInterface
   */
  public function contact(Request $request, MailerInterface $mailer): Response
  {
    $form = $this->createForm(ContactType::class);
    $contact = $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
      $email = (new TemplatedEmail())
          ->from($contact->get('email')->getData())
          ->to('myaddress@gmail.com')
          ->subject('Contact from the Petites Annonces website')
          ->htmlTemplate('emails/contact.html.twig')
          ->context([
              'mail' => $contact->get('email')->getData(),
              'subject' => $contact->get('subject')->getData(),
              'message' => $contact->get('message')->getData()
          ]);

      $mailer->send($email);

      $this->addFlash('success', 'Your email has been sent');
      return $this->redirectToRoute('contact');
    }

    return $this->render('main/contact.html.twig', [
        'form' => $form->createView()
    ]);
  }
}
