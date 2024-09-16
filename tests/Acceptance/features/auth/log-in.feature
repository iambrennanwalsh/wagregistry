Feature: Log In
  In order to log in,
  As a logged out user,
  I must enter my valid credentials into the log in form.

  Background:
    Given I am on "/login"

  Scenario: A user provides valid credentials
    Given a "testuser@wagregistry.com" exists
    When I fill in "Email Address" with "testuser@wagregistry.com"
    And I fill in "Password" with "shirehobbit"
    And I click "Log In"
    Then I should be on "/account"

  Scenario: A user provides invalid credentials
    When I fill in "Email Address" with "notauser@wagregistry.com"
    And I fill in "Password" with "shirehobbit"
    And I click "Log In"
    Then I should be on "/login"
    And I should see a form error saying "Invalid credentials."
