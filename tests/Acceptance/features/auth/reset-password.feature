Feature: Reset Password
  In order to reset my forgotten password,
  As a logged out user,
  I must click on a link provided in the "Recover your Wagregistry account." email.

  Background:
    Given a test user exists

  Scenario: A user successfuly resets their password.
    When I click the emailed password recovery link
    And I fill in "New Password" with "newpass"
    And I fill in "Confirm Password" with "newpass"
    And I click "Reset Password"
    Then I should be on "/login"
    And I should see a "success" notification saying "Your password has been updated."
    And I should recieve an email with subject "Your password has been updated."

  Scenario: A user visits the reset route with an invalid url.
    When I go to "/reset"
    Then I should be on "/forgot"
    And I should see a "danger" notification saying "Sorry, but something went wrong."
