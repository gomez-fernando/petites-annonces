<?php
namespace App\EventListener;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;

class MaintenanceListener extends AbstractController
{
  private $maintenance;
  private $twig;

  public function __construct($maintenance, Environment $twig){
    $this->maintenance = $maintenance;
    $this->twig = $twig;
  }

  public function onKernelRequest(RequestEvent $event)
  {
    // we verify if the file .maintenace exists
//    dd($this->maintenance);
    if(!file_exists($this->maintenance)){
      return;
    }

    $event->setResponse(
        new Response(
//            '<h1>This site is under maintenance</h1>',
                    $this->twig->render('maintenance/maintenance.html.twig'),
            Response::HTTP_SERVICE_UNAVAILABLE
        )
    );

    // we stop the treatment of events
    $event->stopPropagation();
  }
}