<?php

namespace App\Entity;

use \DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass, ORM\HasLifecycleCallbacks]
class BaseEntity
{
  #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
  protected int $id;

  #[ORM\Column(type: 'datetime')]
  #[Assert\Type("\DateTimeInterface")]
  protected DateTime $createdAt;

  #[ORM\Column(type: 'datetime')]
  #[Assert\Type("\DateTimeInterface")]
  protected DateTime $updatedAt;

  public function __construct(array $args = [])
  {
    $this->createdAt = new DateTime();
    $this->updatedAt = $this->createdAt;
    if (!empty($args)) {
      $this->factory($args);
    }
  }

  public function factory(array $args)
  {
    foreach ($args as $property => $argument) {
      if (property_exists($this, $property)) {
        $method = 'set' . ucfirst($property);
        if (method_exists($this, $method)) {
          $this->{$method}($argument);
        } else {
          $this->{$property} = $argument;
        }
      }
    }
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getCreatedAt(): DateTime
  {
    return $this->createdAt;
  }

  public function getUpdatedAt(): DateTime
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(DateTime $updatedAt): void
  {
    $this->updatedAt = $updatedAt;
  }

  #[ORM\PreUpdate]
  public function preUpdate()
  {
    $this->setUpdatedAt(new DateTime());
  }
}
