<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class AuthRateLimiter
{

  private Request $request;
  private LimiterInterface $authLimiter;

  public function __construct(RateLimiterFactory $authLimiter, RequestStack $requestStack)
  {
    $this->request = $requestStack->getCurrentRequest();
    $this->authLimiter = $authLimiter->create(
      $this->request->getClientIp()
    );
  }

  public function reset()
  {
    $this->authLimiter->reset();
  }

  public function canConsume()
  {
    $limit = $this->consume();
    return $limit->isAccepted();
  }

  public function getRetryAfter(): int
  {
    $limit = $this->consume();
    return round(($limit->getRetryAfter()->getTimestamp() - time()) / 60);
  }

  public function availableTokens(): int
  {
    $limit = $this->consume();
    return $limit->getRemainingTokens();
  }

  public function consume(int $num = 0)
  {
    return $this->authLimiter->consume($num);
  }

}
