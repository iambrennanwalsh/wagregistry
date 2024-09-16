<?php

namespace App\Notification\Notifications;

use App\Entity\User;
use App\Notification\FlashMessageImportanceMapper;
use App\Notification\Notification;

class AuthNotification
{

  public function __construct(private Notification $notification)
  {
  }

  /**
   * Sent to a user to inform them of auth ban.
   */
  public function authBan(string $ttl)
  {
    $notification = clone $this->notification;
    $notification->subject(
      "You have attempted to authenticate too many times. Try again in $ttl minutes."
    );
    $notification->channels(['browser']);
    $notification->importance(FlashMessageImportanceMapper::DANGER);
    return $notification;
  }

  /**
   * Sent to a user to warn them of potential auth ban.
   */
  public function authWarning(string $tries)
  {
    $notification = clone $this->notification;
    $notification->subject(
      "You have failed to login " . 5 - $tries . " times. You have " .
      $tries .
      ' remaining attempts before a temporary one hour ban is implemented.'
    );
    $notification->channels(['browser']);
    $notification->importance(FlashMessageImportanceMapper::WARNING);
    return $notification;
  }

  /**
   * Sent to a user when they successfully verify their email.
   */
  public function emailVerified()
  {
    $notification = clone $this->notification;
    $notification->subject('Your email has been verified.');
    $notification->channels(['browser']);
    $notification->importance(FlashMessageImportanceMapper::SUCCESS);
    return $notification;
  }

  /**
   * Sent to a user when they successfully request a  password reset.
   */
  public function forgotPassword(User $user)
  {
    $notification = clone $this->notification;
    $notification->emailSubject('Reset your password.');
    $notification->subject(
      'We just sent an email to ' .
      $user->getEmail() .
      ' containing further instructions.'
    );
    $notification->template('/auth/forgot-password');
    $notification->context(['user' => $user]);
    $notification->channels(['browser', 'email']);
    $notification->importance(FlashMessageImportanceMapper::SUCCESS);
    return $notification;
  }

  /**
   * Sent to a user when they successfully reset their password.
   */
  public function passwordReset(User $user)
  {
    $notification = clone $this->notification;
    $notification->subject('Your password has been reset.');
    $notification->template('/auth/password-reset');
    $notification->context(['user' => $user]);
    $notification->channels(['browser', 'email']);
    $notification->importance(FlashMessageImportanceMapper::SUCCESS);
    return $notification;
  }

  /**
   * Sent to a user upon Signup. Can be resent.
   */
  public function verifyEmail(User $user)
  {
    $notification = clone $this->notification;
    $notification->emailSubject('Verify your email.');
    $notification->subject('Verify your email by clicking the link we sent.');
    $notification->template('/auth/verify-email');
    $notification->context(['user' => $user]);
    $notification->importance(FlashMessageImportanceMapper::INFO);
    $notification->channels(['browser', 'email']);
    return $notification;
  }

  /**
   * Sent to a user upon Signup.
   */
  public function welcome(User $user)
  {
    $notification = clone $this->notification;
    $notification->subject('Welcome to WagRegistry.');
    $notification->template('/auth/welcome');
    $notification->context(['user' => $user]);
    $notification->channels(['email']);
    return $notification;
  }

  /**
   * Sent to the admin upon new user Signup.
   */
  public function newMember(User $user)
  {
    $notification = clone $this->notification;
    $notification->subject('New member Signup.');
    $notification->template('/auth/new-member');
    $notification->context(['user' => $user]);
    $notification->channels(['email']);
    return $notification;
  }

}
