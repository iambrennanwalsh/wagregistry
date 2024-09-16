<?php

namespace App\EventListener;

use App\Event\AuthEvent;
use App\Exception\AlreadyAuthenticatedException;
use App\Exception\AuthBanException;
use App\Notification\FlashMessageImportanceMapper;
use App\Notification\GenericFlashMessageNotification;
use App\Notification\Notifications\AuthNotification;
use App\Security\AuthRateLimiter;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Notifier\Notifier;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

#[
  AsEventListener(
  event: AuthEvent::LOGIN_FAILURE,
  method: 'onAuthLoginFailure'
)
]
#[
  AsEventListener(
  event: AuthEvent::LOGIN_SUCCESS,
  method: 'onAuthLoginSuccess'
)
]
#[
  AsEventListener(
  event: AuthEvent::SIGNUP_FAILURE,
  method: 'onAuthSignupFailure'
)
]
#[
  AsEventListener(
  event: AuthEvent::SIGNUP_SUCCESS,
  method: 'onAuthSignupSuccess'
)
]
#[
  AsEventListener(
  event: AuthEvent::RESET_PASSWORD_FAILURE,
  method: 'onAuthResetPasswordFailure'
)
]
#[
  AsEventListener(
  event: AuthEvent::RESET_PASSWORD_SUCCESS,
  method: 'onAuthResetPasswordSuccess'
)
]
#[
  AsEventListener(
  event: AuthEvent::FORGOT_PASSWORD_SUCCESS,
  method: 'onAuthForgotPasswordSuccess'
)
]
#[
  AsEventListener(
  event: AuthEvent::FORGOT_PASSWORD_FAILURE,
  method: 'onAuthForgotPasswordFailure'
)
]
#[
  AsEventListener(
  event: AuthEvent::VERIFICATION_SUCCESS,
  method: 'onAuthVerificationSuccess'
)
]
#[
  AsEventListener(
  event: AuthEvent::VERIFICATION_FAILURE,
  method: 'onAuthVerificationFailure'
)
]
#[
  AsEventListener(
  event: AuthEvent::RESEND_VERIFICATION,
  method: 'onAuthResendVerification'
)
]
#[AsEventListener(event: 'kernel.exception', method: 'onAuthBan')]
#[AsEventListener(event: 'kernel.exception', method: 'onAlreadyAuthenticated')]
class AuthListener
{
  private Request $request;
  private AuthRateLimiter $authLimiter;
  private Notifier $notifier;
  private AuthNotification $notification;

  public function __construct(
    AuthRateLimiter $authLimiter,
    RequestStack $request,
    NotifierInterface $notifier,
    AuthNotification $notification
  ) {
    $this->request = $request->getCurrentRequest();
    $this->authLimiter = $authLimiter;
    $this->notifier = $notifier;
    $this->notification = $notification;
  }

  private function consume()
  {
    $this->authLimiter->consume(1);
    $tries = $this->authLimiter->availableTokens();
    if ($tries && $tries <= 2) {
      $notification = $this->notification->authWarning($tries);
      $this->notifier->send($notification);
    }
  }

  public function onAuthBan(ExceptionEvent $event): void
  {
    $exception = $event->getThrowable();
    if ($exception instanceof AuthBanException) {
      $ttl = $exception->getRetryAfter();
      $notification = $this->notification->authBan($ttl);
      $this->notifier->send($notification);
      $event->setResponse(new RedirectResponse('/'));
    }
  }

  public function onAlreadyAuthenticated(ExceptionEvent $event): void
  {
    $exception = $event->getThrowable();
    if ($exception instanceof AlreadyAuthenticatedException) {
      $event->setResponse(new RedirectResponse('/'));
    }
  }

  public function onAuthLoginSuccess(AuthEvent $event): void
  {
    $this->authLimiter->reset();
  }

  public function onAuthLoginFailure(AuthEvent $event): void
  {
    $this->consume();
    $error = $event->getData()['error'];
    $notification = new GenericFlashMessageNotification(
      $error,
      FlashMessageImportanceMapper::DANGER
    );
    $this->notifier->send($notification);
  }

  public function onAuthSignupSuccess(
    AuthEvent $event
  ): void {
    $user = $event->getUser();
    $recipient = new Recipient($user->getEmail());
    $adminRecipient = $this->notifier->getAdminRecipients();
    $welcomeNotification = $this->notification->welcome($user);
    $verifyEmailNotification = $this->notification->verifyEmail($user);
    $newMemberNotification = $this->notification->newMember($user);
    $this->notifier->send($verifyEmailNotification, $recipient);
    $this->notifier->send($welcomeNotification, $recipient);
    $this->notifier->send($newMemberNotification, ...$adminRecipient);
  }

  public function onAuthSignupFailure(
    AuthEvent $event
  ): void {
    $this->consume();
    foreach ($event->getData()['error'] as $error) {
      $notification = new GenericFlashMessageNotification(
        $error->getMessage(),
        FlashMessageImportanceMapper::DANGER
      );
      $this->notifier->send($notification);
    }
  }

  public function onAuthResetPasswordSuccess(
    AuthEvent $event
  ): void {
    $user = $event->getUser();
    $recipient = new Recipient($user->getEmail());
    $notification = $this->notification->passwordReset($user);
    $this->notifier->send($notification, $recipient);
  }

  public function onAuthResetPasswordFailure(
    AuthEvent $event
  ): void {
    $notification = new GenericFlashMessageNotification(
      'Something went wrong. Please try again.',
      FlashMessageImportanceMapper::DANGER
    );
    $this->notifier->send($notification);
  }

  public function onAuthForgotPasswordSuccess(
    AuthEvent $event
  ): void {
    $user = $event->getUser();
    $recipient = new Recipient($user->getEmail());
    $notification = $this->notification->forgotPassword($user);
    $this->notifier->send($notification, $recipient);
  }

  public function onAuthForgotPasswordFailure(
    AuthEvent $event
  ): void {
    $email = $event->getData()['email'];
    $notification = new GenericFlashMessageNotification(
      "A user with the email address '$email' wasn't found.",
      FlashMessageImportanceMapper::DANGER
    );
    $this->notifier->send($notification);
  }

  public function onAuthVerificationSuccess(
    AuthEvent $event
  ): void {
    $verificationNotification = $this->notification->emailVerified();
    $this->notifier->send($verificationNotification);
  }

  public function onAuthVerificationFailure(
    AuthEvent $event
  ): void {
    $notification = new GenericFlashMessageNotification(
      'Something went wrong. Please try again.',
      FlashMessageImportanceMapper::DANGER
    );
    $this->notifier->send($notification);
  }

  public function onAuthResendVerification(
    AuthEvent $event
  ): void {
    $user = $event->getUser();
    $recipient = new Recipient($user->getEmail());
    $notification = $this->notification->verifyEmail($user);
    $this->notifier->send($notification, $recipient);
  }
}
