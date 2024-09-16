<?php

namespace App\EventListener;

use App\Event\SupportEvent;
use App\Notification\Notifications\SupportNotification;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Notifier\Notifier;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

#[
  AsEventListener(
  event: SupportEvent::TICKET_CREATED,
  method: 'onSupportTicketCreated'
)
]
#[
  AsEventListener(
  event: SupportEvent::USER_REPLIED,
  method: 'onUserSupportMessageCreated'
)
]
#[
  AsEventListener(
  event: SupportEvent::ADMIN_REPLIED,
  method: 'onAdminSupportMessageCreated'
)
]
class SupportListener
{

  private Notifier $notifier;
  private Request $request;
  private SupportNotification $notification;

  public function __construct(RequestStack $request, NotifierInterface $notifier, SupportNotification $notification)
  {
    $this->notifier = $notifier;
    $this->request = $request->getCurrentRequest();
    $this->notification = $notification;
  }

  public function onSupportTicketCreated(SupportEvent $event)
  {
    $ticket = $event->getSupportTicket();
    $recipient = new Recipient($ticket->getUser()->getEmail());
    $adminRecipient = $this->notifier->getAdminRecipients();
    $notification = $this->notification->newSupportTicket($ticket);
    $adminNotification = $this->notification->adminNewSupportTicket($ticket);
    $this->notifier->send($notification, $recipient);
    $this->notifier->send($adminNotification, ...$adminRecipient);
  }

  public function onUserSupportMessageCreated(SupportEvent $event)
  {
    $ticket = $event->getSupportTicket();
    $message = $ticket->getMessages()->last();
    $recipient = new Recipient($ticket->getUser()->getEmail());
    $adminRecipient = $this->notifier->getAdminRecipients();
    $userNotification = $this->notification->newUserSupportMessage($message);
    $adminNotification = $this->notification->adminNewUserSupportMessage($message);

    $this->notifier->send($userNotification, $recipient);
    $this->notifier->send($adminNotification, ...$adminRecipient);
  }

  public function onAdminSupportMessageCreated(SupportEvent $event)
  {
    $ticket = $event->getSupportTicket();
    $message = $ticket->getMessages()->last();
    $recipient = new Recipient($ticket->getUser()->getEmail());
    $userNotification = $this->notification->newAdminSupportMessage($message);

    $this->notifier->send($userNotification, $recipient);
  }
}
