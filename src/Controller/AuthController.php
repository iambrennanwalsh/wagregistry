<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\AuthEvent;
use App\Repository\UserRepository;
use App\Security\AuthAttributes;
use App\Security\Authenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Rompetomp\InertiaBundle\Service\InertiaInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(name: 'auth.')]
class AuthController extends InertiaController
{
  private UserRepository $userRepository;
  private ValidatorInterface $validator;

  public function __construct(
    InertiaInterface $inertia,
    EventDispatcherInterface $dispatcher,
    ValidatorInterface $validator,
    UserRepository $userRepository,
  ) {
    parent::__construct($inertia, $dispatcher);
    $this->validator = $validator;
    $this->userRepository = $userRepository;
  }

  #[Route(path: '/login', methods: ['GET', 'POST'], name: 'login')]
  #[IsGranted('auth')]
  public function login(Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
  {
    $props = [];
    if ($request->hasSession()) {
      $session = $request->getSession();
      $props['lastEmail'] = $session->get(AuthAttributes::LAST_USERNAME, '');
      $props['errors']['email'] = $session->get(AuthAttributes::LAST_ERROR, '');
    }
    $props['csrf'] = $csrfTokenManager->getToken('auth')->getValue();
    return $this->inertia('auth.login', $props);
  }

  #[Route(path: '/signup', methods: ['GET', 'POST'], name: 'signup')]
  #[IsGranted('auth')]
  public function signup(Request $request, Security $security): Response
  {
    $errors = [];
    $session = $request->getSession();
    $oauthUser = $session->remove(AuthAttributes::OAUTH_USER);
    if ($request->isMethod('post')) {
      $content = json_decode($request->getContent(), true);
      $user = new User($content);
      $violations = $this->validator->validate($user);
      $event = new AuthEvent($user, ['error' => $violations]);
      if (count($violations)) {
        foreach ($violations as $violation) {
          $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }
        $this->dispatcher->dispatch(
          $event,
          AuthEvent::SIGNUP_FAILURE
        );
      } else {
        $this->userRepository->save($user, true);
        $this->dispatcher->dispatch(
          $event,
          AuthEvent::SIGNUP_SUCCESS
        );
        return $security->login($user, Authenticator::class);
      }
    }
    return $this->inertia('auth.signup', [
      'oauth' => $oauthUser,
      'errors' => $errors
    ]);
  }

  #[Route('/forgot', methods: ['GET', 'POST'], name: 'forgot')]
  #[IsGranted('auth')]
  public function forgot(Request $request): Response
  {
    // Check if the request method is POST. This means the forgot password form was submitted.
    if ($request->isMethod('post')) {
      // Decode the request body as a PHP array.
      $content = json_decode($request->getContent(), true);
      // Get the user associated with the submmited email.
      $user = $this->userRepository->findOneBy(['email' => $content['email']]);
      // Create an event.
      $event = new AuthEvent($user, $content);
      // Dispatch success or failure events depending on wether a user was found with the submitted email.
      $this->dispatcher->dispatch(
        $event,
        $user
        ? AuthEvent::FORGOT_PASSWORD_SUCCESS
        : AuthEvent::FORGOT_PASSWORD_FAILURE
      );
    }
    // Return the response.
    return $this->inertia('auth.forgot');
  }

  #[Route('/reset', methods: ['GET', 'POST'], name: 'reset')]
  #[IsGranted('auth')]
  public function reset(Request $request, CsrfTokenManagerInterface $csrfTokenManager)
  {
    // A user can reach this controller via 3 possibilities.
    //   1. A user submits the reset password form.
    //   2. A user clicked on emailed reset password link.
    //   3. A user uses an invalid url.

    // If a user submits the reset password form, the request method should be POST.
    if ($request->isMethod('post')) {
      // Decode the JSON request body as PHP array.
      $content = json_decode($request->getContent(), true);
      // To ensure the user actually submitted the form, we validate the csrf token.
      if ($this->isCsrfTokenValid('reset-password', $content['csrf'])) {
        // So the form WAS submitted.
        // Now get the user with matching id.
        $user = $this->userRepository->find($content['id']);
        // Lastly we check the users resetPasswordToken matches the hash.
        // This verifies that the id recieved in the request body wasn't modified.
        if ($user->getResetPasswordToken() === $content['hash']) {
          // Update the password.
          $user->setPassword($content['password']);
          // Update the resetPasswordToken to a new random value.
          $user->setResetPasswordToken();
          // Save it.
          $this->userRepository->save($user, true);
          // Send it.
          $this->dispatcher->dispatch(
            new AuthEvent($user),
            AuthEvent::RESET_PASSWORD_SUCCESS
          );
          // Send em to the login page.
          return $this->redirectToRoute('auth.login');
        }
      }
      // Since the request method wasn't POST it can only be GET.
      // Lets validate this request by looking for the id and hash query parameters.
    } elseif (($hash = $request->query->get('hash')) && ($id = $request->query->get('id'))) {
      // Get the user associated with the id query parameter.
      $user = $this->userRepository->find($id);
      // Does the user exist?
      // Does their resetPasswordToken match the query paramater hash?
      if ($user && $user->getResetPasswordToken() === $hash) {
        // Its valid. So lets create a csrf token.
        $csrf = $csrfTokenManager->getToken('reset-password')->getValue();
        // And then lets pass these values to the response.
        return $this->inertia('auth.reset', ['csrf' => $csrf, 'hash' => $hash, 'id' => $id]);
      }
    }
    // If we've reached this point, then the user visited /reset via a bad url.
    // Dispatch an event.
    $this->dispatcher->dispatch(
      new AuthEvent(),
      AuthEvent::RESET_PASSWORD_FAILURE
    );
    // Redirect back to the forgot page.
    return $this->redirectToRoute('auth.forgot');
  }

  #[Route('/verify', methods: ['GET'], name: 'verify')]
  public function verify(Request $request)
  {
    if (
      ($hash = $request->query->get('hash')) && ($id = $request->query->get('id'))
    ) {
      $user = $this->userRepository->find($id);
      if ($user && $user->getEmailConfirmationToken() === $hash) {
        $user->setEmailConfirmationToken();
        $user->setEmailConfirmation(true);
        $this->userRepository->save($user, true);
        $event = new AuthEvent($user);
        $this->dispatcher->dispatch(
          $event,
          AuthEvent::VERIFICATION_SUCCESS
        );
      } else {
        $this->dispatcher->dispatch(
          new AuthEvent(),
          AuthEvent::VERIFICATION_FAILURE
        );
      }
    }
    return $this->redirectToRoute('frontend.home');
  }

  #[Route('/verify/resend', methods: ['GET'], name: 'verify.resend')]
  public function resend(Request $request)
  {
    $user = $this->getUser();
    if ($user && $user->getEmailConfirmation() !== true) {
      $event = new AuthEvent($user);
      $this->dispatcher->dispatch(
        $event,
        AuthEvent::RESEND_VERIFICATION
      );
    }
    $target = $request->headers->get('referer', '/');
    return $this->redirect($target);
  }

  #[Route('/oauth/facebook', name: 'facebook')]
  #[Route('/oauth/google', name: 'google')]
  #[Route('/oauth/apple', name: 'apple')]
  #[IsGranted('auth')]
  public function oauth(Request $request, ClientRegistry $clientRegistry)
  {
    $route = $request->attributes->get('_route');
    $name = explode('.', $route)[1];

    return $clientRegistry
      ->getClient($name)
      ->redirect([
        'public_profile',
        'email'
      ], []);
  }

  #[Route('/oauth/facebook/check', name: 'facebook.check')]
  #[Route('/oauth/google/check', name: 'google.check')]
  #[Route('/oauth/apple/check', name: 'apple.check')]
  #[IsGranted('auth')]
  public function oauthCheck(Request $request, ClientRegistry $clientRegistry)
  {
  }

  #[Route('/logout', name: 'logout', methods: ['GET'])]
  public function logout(): never
  {
    throw new \Exception('Don\'t forget to activate logout in security.yaml');
  }

}
