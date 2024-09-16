<?php
namespace App\Event;

use App\Entity\SupportTicket;

class SupportEvent
{

  /** The TICKET_CREATED event is dispatched when a new support ticket is created. */
  const string TICKET_CREATED = 'support.ticket_created';

  /** The USER_REPLIED event is dispatched when a user adds a reply on a ticket. */
  const string USER_REPLIED = 'support.user_replied';

  /** The ADMIN_REPLIED event is dispatched when an admin adds a reply on a ticket. */
  const string ADMIN_REPLIED = 'support.admin_replied';

  private ?SupportTicket $supportTicket;
  private mixed $data;

  public function __construct(?SupportTicket $supportTicket = null, mixed $data = [])
  {
    $this->supportTicket = $supportTicket;
    $this->data = $data;
  }

  public function getSupportTicket()
  {
    return $this->supportTicket;
  }

  public function getData()
  {
    return $this->data;
  }

}
