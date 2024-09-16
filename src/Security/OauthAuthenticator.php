<?php

namespace App\Security;

use App\Entity\User;
use App\Event\AuthEvent;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use KnpU\OAuth2ClientBundle\Security\Exception\FinishRegistrationException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class OauthAuthenticator extends OAuth2Authenticator
{
  private $clientRegistry;
  private $entityManager;
  private $router;
  private $dispatcher;

  public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router, EventDispatcherInterface $dispatcher)
  {
    $this->clientRegistry = $clientRegistry;
    $this->entityManager = $entityManager;
    $this->router = $router;
    $this->dispatcher = $dispatcher;
  }

  public function supports(Request $request): ?bool
  {
    $clients = ['facebook', 'google', 'apple'];
    $route = $request->attributes->get('_route');
    $client = explode('.', $route)[1];
    return str_starts_with($route, 'auth.') && str_ends_with($route, '.check') && in_array($client, $clients);
  }

  public function authenticate(Request $request): Passport
  {
    $route = $request->attributes->get('_route');
    $name = explode('.', $route)[1];

    $client = $this->clientRegistry->getClient($name);
    $accessToken = $this->fetchAccessToken($client);
    $oauthUser = $client->fetchUserFromToken($accessToken);

    return new SelfValidatingPassport(
      new UserBadge($accessToken->getToken(), function () use ($oauthUser, $name) {
        $repository = $this->entityManager->getRepository(User::class);
        $user = $repository->findOneBy(["{$name}Id" => $oauthUser->getId()]);
        if ($user) {
          return $user;
        }
        $user = $repository->findOneBy(['email' => $oauthUser->getEmail()]);
        if ($user) {
          $setter = "set" . ucfirst($name) . "Id";
          $user->{$setter}($oauthUser->getId());
          $this->entityManager->flush();
          return $user;
        }

        throw new FinishRegistrationException($oauthUser);

      })
    );
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
  {
    $event = new AuthEvent($token->getUser());
    $this->dispatcher->dispatch($event, AuthEvent::LOGIN_SUCCESS);
    $targetUrl = $this->router->generate('account.account');
    return new RedirectResponse($targetUrl);
  }

  public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
  {
    $targetUrl = $this->router->generate('auth.login');
    if ($exception instanceof FinishRegistrationException) {
      $targetUrl = $this->router->generate('auth.signup');
      $session = $request->getSession();
      $session->set(AuthAttributes::OAUTH_USER, $exception->getUserInformation());
    }
    return new RedirectResponse($targetUrl);
  }
}
