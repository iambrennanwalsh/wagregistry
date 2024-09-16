<?php

namespace App\Exception;

class AlreadyAuthenticatedException extends \Exception
{

  public function __construct()
  {
    parent::__construct($message = '', $code = 0, $previousException = null);
  }
}
