<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class InertiaEvent extends Event
{

  /**
   * Inertia is preparing to server render.
   */
  const string RENDER = 'events.inertia.render';


  private ?User $user;

  public function __construct(?User $user)
  {
    $this->user = $user;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }
}
