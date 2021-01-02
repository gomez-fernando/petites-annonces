<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Form\RegistrationFormType;
use App\Form\SelfEditUserType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="user_")
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig');
    }

  /**
   * @Route("/announce/add", name="add_annonce")
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
   * @Route("/edit/profile", name="edit_profile")
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

      $this->addFlash('success', 'Profile updated successfully');
      return $this->redirectToRoute('user');
    }

    return $this->render('user/edit-profile.html.twig', [
        'form' => $form->createView()
    ]);
  }

  /**
   * @Route("/edit/password", name="edit_password")
   * @param Request $request
   * @param UserPasswordEncoderInterface $encoder
   * @return Response
   */
  public function userEditPassword(Request $request, UserPasswordEncoderInterface $encoder): Response
  {
    if($request->isMethod('POST')){
      $em = $this->getDoctrine()->getManager();

      $user = $this->getUser();

      if($request->request->get('pass') == $request->request->get('pass2') ){
          $user->setPassword($encoder->encodePassword($user, $request->request->get('pass')));
          $em->flush();
          $this->addFlash('success', 'Password changed!');

          return $this->redirectToRoute('user');

      }else{
        $this->addFlash('error', 'The passwords are not equal');
      }
    }

    return $this->render('user/edit-password.html.twig');
  }

  /**
   * @Route("/data", name="data")
   */
  public function userData(): Response
  {
    return $this->render('user/data.html.twig');
  }

  /**
   * @Route("/data/download", name="data_download")
   */
  public function userDataDownload(): Response
  {
    // define the pdf options
    $pdfOptions = new Options();

    // default police
    $pdfOptions->set('defaultFont', 'Arial');
    $pdfOptions->setIsRemoteEnabled(true);

    // instance dompdf
    $dompdf = new Dompdf($pdfOptions);
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => FALSE,
            'verify_peer_name' => FALSE,
            'verify_self_signed' => FALSE
        ]
    ]);

    $dompdf->setHttpContext($context);

    // generate HTML
    $html = $this->renderView('user/download.html.twig');

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // generate a file name
    $file = 'user-data-' . $this->getUser()->getName() . '-' . $this->getUser()->getLastname() . '.pdf';

    // send pdf to browser
    $dompdf->stream($file, [
        'Attachment' => true
    ]);

    return new Response();
  }

//  /**
//   * @Route("/supprime/image/{id}", name="annonces_delete_image", methods={"DELETE"})
//   */
//  public function deleteImage(Images $image, Request $request){
//    $data = json_decode($request->getContent(), true);
//
//    // On vérifie si le token est valide
//    if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
//      // On récupère le nom de l'image
//      $nom = $image->getName();
//      // On supprime le fichier
//      unlink($this->getParameter('images_directory').'/'.$nom);
//
//      // On supprime l'entrée de la base
//      $em = $this->getDoctrine()->getManager();
//      $em->remove($image);
//      $em->flush();
//
//      // On répond en json
//      return new JsonResponse(['success' => 1]);
//    }else{
//      return new JsonResponse(['error' => 'Token Invalide'], 400);
//    }
//  }
}
