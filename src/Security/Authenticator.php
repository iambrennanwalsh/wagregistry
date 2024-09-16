<?php

namespace App\Security;

use App\Event\AuthEvent;
use App\Exception\AuthBanException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\HttpUtils;

class Authenticator implements InteractiveAuthenticatorInterface, AuthenticationEntryPointInterface
{
  private AuthRateLimiter $limiter;
  private HttpUtils $httpUtils;
  private UserProviderInterface $userProvider;
  private EventDispatcherInterface $dispatcher;

  public function __construct(AuthRateLimiter $limiter, HttpUtils $httpUtils, UserProviderInterface $userProvider, EventDispatcherInterface $dispatcher)
  {
    $this->limiter = $limiter;
    $this->httpUtils = $httpUtils;
    $this->userProvider = $userProvider;
    $this->dispatcher = $dispatcher;
  }

  public function supports(Request $request): ?bool
  {
    if (
      !str_contains($request->getRequestFormat() ?? '', 'json') &&
      !str_contains($request->getContentTypeFormat() ?? '', 'json')
    ) {
      return false;
    }

    return $request->isMethod('POST') && $this->httpUtils->checkRequestPath($request, 'auth.login');
  }

  public function authenticate(Request $request): Passport
  {
    try {
      $credentials = json_decode($request->getContent(), true);
      if (!is_array($credentials)) {
        throw new BadRequestHttpException('Invalid JSON.');
      }

      try {
        if (!is_string($credentials['email']) || $credentials['email'] === '') {
          throw new BadRequestHttpException(sprintf('The key "%s" must be a string.', 'email'));
        }

      } catch (AccessException $e) {
        throw new BadRequestHttpException(sprintf('The key "%s" must be provided.', 'email'), $e);
      }

      try {
        if (!is_string($credentials['password']) || $credentials['password'] === '') {
          throw new BadRequestHttpException(sprintf('The key "%s" must be a string.', 'password'));
        }
      } catch (AccessException $e) {
        throw new BadRequestHttpException(sprintf('The key "%s" must be provided.', 'password'), $e);
      }

    } catch (BadRequestHttpException $exception) {
      $request->setRequestFormat('json');
      throw $exception;
    }

    $request->getSession()->set(AuthAttributes::LAST_USERNAME, $credentials['email']);


    $userBadge = new UserBadge($credentials['email'], $this->userProvider->loadUserByIdentifier(...));
    $passport = new Passport($userBadge, new PasswordCredentials($credentials['password']), [new RememberMeBadge($credentials)]);
    $passport->addBadge(new CsrfTokenBadge('auth', $credentials['csrf_token']));
    $passport->addBadge(new PasswordUpgradeBadge($credentials['password'], $this->userProvider));

    return $passport;
  }

  public function createToken(Passport $passport, string $firewallName): TokenInterface
  {
    return new UsernamePasswordToken($passport->getUser(), $firewallName, $passport->getUser()->getRoles());
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
  {
    $event = new AuthEvent($token->getUser());
    $this->dispatcher->dispatch($event, $event::LOGIN_SUCCESS);
    return $this->httpUtils->createRedirectResponse($request, 'account.account');
  }

  public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
  {
    $event = new AuthEvent(null, ['error' => "Invalid credentials."]);
    $this->dispatcher->dispatch($event, $event::LOGIN_FAILURE);
    $request->getSession()->set(AuthAttributes::LAST_ERROR, "Invalid Credentials");

    if (false === $this->limiter->canConsume()) {
      throw new AuthBanException($this->limiter->getRetryAfter());
    }

    return $this->httpUtils->createRedirectResponse($request, 'auth.login');
  }

  public function isInteractive(): bool
  {
    return true;
  }

  public function start(Request $request, AuthenticationException $authException = null): Response
  {
    return $this->httpUtils->createRedirectResponse($request, 'auth.login');
  }
}
