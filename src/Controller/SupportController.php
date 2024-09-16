<?php

namespace App\Controller;

use App\Entity\SupportMessage;
use App\Entity\SupportTicket;
use App\Event\SupportEvent;
use Doctrine\ORM\EntityManagerInterface;
use Rompetomp\InertiaBundle\Service\InertiaInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route(name: 'support.')]
#[IsGranted('support', new Expression('request.getClientIp()'))]
class SupportController extends InertiaController
{

  protected EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher, InertiaInterface $inertia, RequestStack $requestStack)
  {
    $this->entityManager = $entityManager;
    parent::__construct($inertia, $dispatcher);
  }

  #[Route("/support", name: 'support')]
  public function support(Request $request)
  {
    if ($request->isMethod('post')) {
      $content = json_decode($request->getContent(), true);

      if ($this->isCsrfTokenValid('support', $content['csrf'])) {
        $user = $this->getUser();
        $ticket = new SupportTicket(['subject' => $content['subject'], 'user' => $user]);
        $message = new SupportMessage(['message' => $content['message'], 'ticket' => $ticket]);
        $ticket->addMessage($message);
        $user->addSupportTicket($ticket);
        $this->entityManager->persist($ticket);
        $this->entityManager->flush();
        $this->addFlash("success", "Your ticket was created and recieved!");
        $event = new SupportEvent($ticket);
        $this->dispatcher->dispatch($event, SupportEvent::TICKET_CREATED);

        return $this->redirect('/account/ticket/' . $ticket->getId());
      }
    }
    return $this->inertia('support.support');
  }

  #[Route("/support/tickets", name: 'tickets')]
  public function tickets()
  {
    $user = $this->getUser();
    $tickets = $user->getSupportTickets();
    return $this->inertia('support.tickets', [
      'tickets' => $tickets
    ]);
  }

  #[Route("/account/ticket/{id}", name: "ticket")]
  #[IsGranted("view", subject: "ticket")]
  public function ticket(SupportTicket $ticket, Request $request)
  {
    if ($request->isMethod('post')) {
      $content = json_decode($request->getContent(), true);
      if ($this->isCsrfTokenValid('support', $content['csrf'])) {
        $admin = $content['admin'] === 'true';
        $message = new SupportMessage(['message' => $content['message'], 'admin' => $admin, 'ticket' => $ticket]);
        $ticket->addMessage($message);
        $ticket->setStatus($admin);
        $this->entityManager->persist($message);
        $this->entityManager->flush();
        $event = new SupportEvent($ticket);
        $this->dispatcher->dispatch($event, $admin ? SupportEvent::ADMIN_REPLIED : SupportEvent::USER_REPLIED);
      }
    } elseif ($ticket->getStatus()) {
      $ticket->setStatus(false);
      $this->entityManager->flush();
    }
    return $this->inertia('support.ticket', [
      'ticket' => $ticket
    ]);
  }
}
