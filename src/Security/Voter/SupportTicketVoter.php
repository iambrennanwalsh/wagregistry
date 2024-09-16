<?php

namespace App\Security\Voter;

use App\Entity\SupportTicket;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SupportTicketVoter extends Voter
{

  public function __construct(private Security $security)
  {
  }

  const string TOKEN = 'support';

  protected function supports($attribute, $subject): bool
  {
    if ($attribute !== self::TOKEN || !$subject instanceof SupportTicket) {
      return false;
    }
    return true;
  }

  protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
  {
    $user = $token->getUser();
    if (!$user instanceof UserInterface) {
      return false;
    }

    if ($this->security->isGranted('ROLE_ADMIN')) {
      return true;
    }

    $owner = $subject->getUser();
    return $user == $owner;
  }

}
