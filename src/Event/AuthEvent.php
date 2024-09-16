<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class AuthEvent extends Event
{

  /**
   * A failed login occured.
   */
  const string LOGIN_FAILURE = 'events.auth.login_failure';

  /**s
   * A successful login occured.
   */
  const string LOGIN_SUCCESS = 'events.auth.login_success';

  /**
   * A failed social login occured.
   */
  const string SOCIAL_LOGIN_FAILURE = 'events.auth.social_login_failure';

  /**s
   * A successful social login occured.
   */
  const string SOCIAL_LOGIN_SUCCESS = 'events.auth.social_login_success';

  /**
   * A failed account signup occured.
   */
  const string SIGNUP_FAILURE = 'events.auth.signup_success';

  /**
   * A successful account signup occured.
   */
  const string SIGNUP_SUCCESS = 'events.auth.signup_success';

  /**
   * A user requests to reset their password.
   */
  const string FORGOT_PASSWORD_SUCCESS = 'events.auth.forgot_password_success';

  /**
   * A user provides an inactive username to the forgot password form.
   */
  const string FORGOT_PASSWORD_FAILURE = 'events.auth.forgot_password_failure';

  /**
   * A user successfuly resets their password.
   */
  const string RESET_PASSWORD_SUCCESS = 'events.auth.reset_password_success';

  /**
   * A user attempts to access the reset password link with an invalid url.
   */
  const string RESET_PASSWORD_FAILURE = 'events.auth.reset_password_failure';

  /**
   * A user successfuly verified their email.
   */
  const string VERIFICATION_SUCCESS = 'events.auth.verification_success';

  /**
   * A user accessed the verify endpoint with a bad url.
   */
  const string VERIFICATION_FAILURE = 'events.auth.verification_failure';

  /**
   * A user requests the verification email be resent.
   */
  const string RESEND_VERIFICATION = 'events.auth.resend_verification';

  private ?User $user;
  private $data;

  public function __construct(?User $user = null, mixed $data = [])
  {
    $this->user = $user;
    $this->data = $data;
  }

  public function getUser()
  {
    return $this->user;
  }

  public function getData()
  {
    return $this->data;
  }
}
