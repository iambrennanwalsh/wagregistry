<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class SupportMessage extends BaseEntity
{

  #[ORM\Column(type: Types::TEXT)]
  private string $message;

  #[ORM\Column(type: Types::BOOLEAN)]
  private bool $admin;

  #[ORM\ManyToOne(targetEntity: SupportTicket::class, inversedBy: 'messages')]
  private SupportTicket $ticket;

  public function __construct(array $args = array())
  {
    $this->admin = false;
    parent::__construct($args);
  }

  public function getMessage(): ?string
  {
    return $this->message;
  }

  public function setMessage(string $message): void
  {
    $this->message = $message;
  }

  public function getAdmin(): ?bool
  {
    return $this->admin;
  }

  public function setAdmin(bool $admin): void
  {
    $this->admin = $admin;
  }

  public function getTicket(): SupportTicket
  {
    return $this->ticket;
  }

  public function setTicket(SupportTicket $ticket): void
  {
    $this->ticket = $ticket;
  }

}
