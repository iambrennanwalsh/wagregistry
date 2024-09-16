<?php

declare(strict_types=1);

namespace App\Tests\Support;

use App\Entity\User;
use App\Repository\UserRepository;

/**
 * Inherited Methods
 *
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor
{
  use _generated\AcceptanceTesterActions;

  /**
   * @Given a :arg1 exists
   */
  public function aFixtureExists($arg1)
  {
  }

  /**
   * @Given I am logged in as :arg1
   */
  public function loggedInAs($arg1)
  {
    $repository = $this->grabService(UserRepository::class);
    $user = $repository->findOneBy(['email', $arg1]);
    $this->amLoggedInAs($user);
  }

  /**
   * @Given I am on :arg1
   * @When I go to :arg1
   */
  public function iNavigateTo($arg1)
  {
    $this->amOnPage($arg1);
  }

  /**
   * @When I click on the emailed account confirmation link
   */
  public function iClickOnTheEmailVerificationLink($arg1)
  {
    $user = $this->grabEntityFromRepository(User::class, ['email' => 'testuser@wagregistry.com']);
    $id = $user->getId();
    $hash = $user->getEmailConfirmationToken();
    $link = "/verify?id=$id&hash=$hash";
    $this->amOnPage($link);
  }

  /**
   * @When I click on the emailed password recovery link
   */
  public function iClickOnThePasswordResetLink($arg1)
  {
    $user = $this->grabEntityFromRepository(User::class, ['email' => 'testuser@wagregistry.com']);
    $id = $user->getId();
    $hash = $user->getResetPasswordToken();
    $link = "/reset?id=$id&hash=$hash";
    $this->amOnPage($link);
  }

  /**
   * @When I fail to login :arg1 times
   */
  public function iFailToLogin($arg1)
  {
    $this->amOnPage('/login');
    $this->fillField('email', "notauser@wagregistry.com");
    $this->fillField('password', "shirehobbit");
    for ($i = 0; $i <= $arg1; $i++) {
      $this->click('Log In');
    }
  }

  /**
   * @When I fill in :arg1 with :arg2
   */
  public function iFillIn($arg1, $arg2)
  {
    $this->fillField(['css' => "input[data-testid='$arg1']"], $arg2);
  }

  /**
   * @When I click :arg1
   */
  public function iClickOn($arg1)
  {
    $this->click('form button[type=submit]');
  }

  /**
   * @When I check :arg1
   */
  public function iCheck($arg1)
  {
    $this->checkOption(['css' => "input[data-testid='$arg1']"]);
  }

  /**
   * @Then I should see a :arg1 element
   */
  public function iShouldSeeElement($arg1)
  {
    $this->seeElement($arg1);
  }

  /**
   * @Then I should see a form error saying :arg1
   */
  public function iShouldSeeFormError($arg1)
  {
    $this->wait(1);
    $this->see($arg1);
  }

  /**
   * @Then I should see an :arg1 notification saying :arg2
   * @Then I should see a :arg1 notification saying :arg2
   */
  public function iShouldSeeANotification($arg1, $arg2)
  {
    $this->see($arg2, ['css' => ".notification.$arg2"]);
  }

  /**
   * @Then I should be on :arg1
   */
  public function iShouldBeOn($arg1)
  {
    $this->wait(1);
    $this->seeCurrentUrlEquals($arg1);
  }

  /**
   * @Then I should recieve an email with subject :arg1
   */
  public function iShouldRecieveAnEmail($arg1)
  {
    $notificationLogger = $this->grabService('notifier.notification_logger_listener');
    if ($notificationLogger) {
      dump($notificationLogger->getEvents());
    }
  }
}
