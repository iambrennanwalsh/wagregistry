<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(name: 'account.')]
#[IsGranted('ROLE_USER')]
class AccountController extends InertiaController
{

  #[Route(path: '/account', name: 'account')]
  public function account(): Response
  {
    return $this->inertia('account.account');
  }
}
