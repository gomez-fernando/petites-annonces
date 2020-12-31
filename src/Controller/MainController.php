<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\AnnonceRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     */
    public function index(): Response
    {
//      dd($this->getUser()->getRoles());
        return $this->render('main/index.html.twig', [
//            'annonces' => $this->annonceRepository->findBy(['active' => true], ['createdAt' => 'DESC', 'title' => 'DESC'], 1)
             'annonces' => $this->annonceRepository->findBy(['active' => true], ['createdAt' => 'DESC', 'title' => 'DESC'], )

        ]);
    }

  /**
   * @Route("/contact", name="contact")
   * @param Request $request
   * @param MailerInterface $mailer
   * @return Response
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
