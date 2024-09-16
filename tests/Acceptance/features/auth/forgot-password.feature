Feature: Forgot Password
  In order to recover my account,
  As a logged out user,
  I must enter the email I signed up with into the forgot password form.

  Background:
    Given a test user exists
    And I am on "/forgot"

  Scenario: A user provides an existing email address.
    When I fill in "Email Address" with "testuser@wagregistry.com"
    And I click "Reset Password"
    Then I should see a "success" notification saying "Check your email for further instructions."
    And I should recieve an email with subject "Recover your account."

  Scenario: A user provides an email that hasn't been active.
    When I fill in "Email Address" with "notauser@wagregistry.com"
    And I click "Reset Password"
    Then I should see a "success" notification saying "Check your email for further instructions."
