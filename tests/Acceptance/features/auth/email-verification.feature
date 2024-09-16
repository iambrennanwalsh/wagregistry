Feature: Email Verification
  In order to verify my email address,
  As a signed up user,
  I must click on the link included in the "Verify Your Email" email.

  Background:
    Given a test user exists
    And I am logged in as "testuser@wagregistry.com"

  Scenario: An unverified user sees the email verification bar.
    When I go to "/"
    Then I should see "Don't forget to verify your email address."

  Scenario: A user successfully verifies their email.
    When I click on the emailed verification link
    Then I should be on "/"
    And I should see a "success" notification saying "Your email has been verified."
    And I dont see "Don't forget to verify your email address."

  Scenario: A user visits the verification route via an invalid url.
    When I go to "/verify"
    Then I should be on "/"
    And I should see an "error" notification saying "Sorry, something went wrong."

  Scenario: A user requests the email verification email be resent.
    When I click on "Resend email"
    Then I should be on "/"
    And I should see a "success" notification saying "Verify your email by clicking the link we sent."
    And I should recieve an email with subject "Please confirm your email."
