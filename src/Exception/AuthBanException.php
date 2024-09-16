<?php

namespace App\Exception;

use \DateTimeImmutable;

class AuthBanException extends \Exception
{

  private int $retryAfter;

  public function __construct(int $retryAfter)
  {
    $this->retryAfter = $retryAfter;
    parent::__construct($message = '', $code = 0, $previousException = null);
  }

  public function getRetryAfter()
  {
    return $this->retryAfter;
  }
}
