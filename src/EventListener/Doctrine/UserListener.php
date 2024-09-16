<?php

namespace App\EventListener\Doctrine;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[
  AsEntityListener(
  event: Events::prePersist,
  method: 'prePersist',
  entity: User::class
)
]
#[
  AsEntityListener(
  event: Events::preUpdate,
  method: 'preUpdate',
  entity: User::class
)
]
class UserListener
{
  private UserPasswordHasherInterface $hasher;

  public function __construct(UserPasswordHasherInterface $hasher)
  {
    $this->hasher = $hasher;
  }

  public function prePersist(User $user, PrePersistEventArgs $event): void
  {
    $this->hashPassword($user, $user->getPassword());
  }

  public function preUpdate(User $user, PreUpdateEventArgs $event): void
  {
    if ($event->hasChangedField('password')) {
      $this->hashPassword($user, $event->getNewValue('password'));
    }
  }

  private function hashPassword(User $user, string $password): void
  {
    $password = $this->hasher->hashPassword($user, $password);
    $user->setPassword($password);
  }
}
