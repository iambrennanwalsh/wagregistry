<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class SupportTicket extends BaseEntity
{

  #[ORM\Column(type: Types::STRING)]
  private string $subject;

  #[ORM\Column(type: Types::BOOLEAN)]
  private bool $status;

  #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'supportTickets')]
  private User $user;

  #[ORM\OneToMany(targetEntity: SupportMessage::class, mappedBy: 'ticket', orphanRemoval: true, cascade: ['persist'])]
  #[ORM\OrderBy(["createdAt" => "ASC"])]
  private Collection $messages;


  public function __construct(array $args = array())
  {
    $this->messages = new ArrayCollection();
    $this->status = false;
    parent::__construct($args);
  }

  public function getSubject(): string
  {
    return $this->subject;
  }

  public function setSubject(string $subject): void
  {
    $this->subject = $subject;
  }

  public function getStatus(): bool
  {
    return $this->status;
  }

  public function setStatus(bool $status): void
  {
    $this->status = $status;
  }

  public function getUser(): User
  {
    return $this->user;
  }

  public function setUser(User $user): void
  {
    $this->user = $user;
  }

  public function getMessages(): Collection
  {
    return $this->messages;
  }

  public function addMessage(SupportMessage $message): void
  {
    if (!$this->messages->contains($message)) {
      $this->messages->add($message);
      $this->status = $message->getAdmin() ? false : true;
    }
  }

  public function removeMessage(SupportMessage $message): void
  {
    $this->messages->removeElement($message);
  }

}
