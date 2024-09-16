<?php

namespace App\Notification\Notifications;

use App\Entity\SupportMessage;
use App\Entity\SupportTicket;
use App\Notification\FlashMessageImportanceMapper;
use App\Notification\Notification;

class SupportNotification
{

  public function __construct(private Notification $notification)
  {
  }

  /**
   * Sent to a user when a new Support Ticket is created.
   */
  public function newSupportTicket(SupportTicket $supportTicket)
  {
    $notification = clone $this->notification;

    $notification->subject("We've recieved your ticket.");
    $notification->template("/support/new-support-ticket");
    $notification->context(["supportTicket" => $supportTicket]);
    $notification->channels(['browser', 'email']);
    $notification->importance(FlashMessageImportanceMapper::SUCCESS);
    return $notification;
  }

  /**
   * Sent to the admin when a new Support Ticket is created.
   */
  public function adminNewSupportTicket(SupportTicket $supportTicket)
  {
    $notification = clone $this->notification;

    $notification->subject("A new support ticket was recieved.");
    $notification->template("/support/admin-new-support-ticket");
    $notification->context(["supportTicket" => $supportTicket]);
    $notification->channels(['email']);
    return $notification;
  }

  /**
   * Sent to a user when they add a reply to a support ticket.
   */
  public function newUserSupportMessage(SupportMessage $supportMessage)
  {
    $notification = clone $this->notification;

    $notification->subject("We've recieved your message.");
    $notification->template("/support/new-user-support-message");
    $notification->context(["supportMessage" => $supportMessage]);
    $notification->channels(['browser', 'email']);
    $notification->importance(FlashMessageImportanceMapper::SUCCESS);
    return $notification;
  }

  /**
   * Sent to the admin when a user adds a reply to a support ticket.
   */
  public function adminNewUserSupportMessage(SupportMessage $supportMessage)
  {
    $notification = clone $this->notification;

    $notification->subject("We've recieved a new reply on support ticket #" . $supportMessage->getId() . ".");
    $notification->template("/support/admin-new-user-support-message");
    $notification->context(["supportMessage" => $supportMessage]);
    $notification->channels(['email']);
    return $notification;
  }

  /**
   * Sent to a user when an admin replies to their support ticket.
   */
  public function newAdminSupportMessage(SupportMessage $supportMessage)
  {
    $notification = clone $this->notification;

    $notification->subject("Theres a new reply on your support ticket.");
    $notification->template("/support/new-admin-support-message");
    $notification->context(["supportMessage" => $supportMessage]);
    $notification->channels(['email']);
    return $notification;
  }

}
