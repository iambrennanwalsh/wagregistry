<?php

namespace App\Security\Voter;

use App\Exception\AlreadyAuthenticatedException;
use App\Exception\AuthBanException;
use App\Security\AuthRateLimiter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * This voter checks wether a user can access the auth routes.
 *
 * There are two cases where a user would not be allowed access:
 *
 * If (The user is logged in)
 *  - AlreadyAuthenticatedException is thrown.
 *
 * ElseIf (The user is rate limited)
 *  - AuthBanException is thrown.
 *
 * Else
 *  - Voter returns true
 */
class AuthEntrypointVoter extends Voter
{

  const string TOKEN = 'auth';

  public function __construct(
    private Security $security,
    private AuthRateLimiter $authLimiter
  ) {
  }

  protected function supports(string $attribute, mixed $subject): bool
  {
    return $attribute === self::TOKEN;
  }

  protected function voteOnAttribute(
    string $attribute,
    mixed $subject,
    TokenInterface $token
  ): bool {

    if ($this->security->isGranted('ROLE_USER')) {
      throw new AlreadyAuthenticatedException();
    }

    if (false === $this->authLimiter->canConsume()) {
      throw new AuthBanException($this->authLimiter->getRetryAfter());
    }

    return true;
  }
}
