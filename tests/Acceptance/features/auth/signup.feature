Feature: Sign Up
  In order to sign up,
  As a logged out user,
  I must enter my information into the sign up form.

  Background:
    Given I am on "/signup"

  Scenario: A user signs up successfuly.
    When I fill in "Email Address" with "newuser@wagregistry.com"
    And I fill in "Name" with "New User"
    And I fill in "Password" with "shirehobbit"
    And I fill in "Confirm Password" with "shirehobbit"
    And I check "I agree to the terms"
    And I click "Sign Up"
    Then I should be on "/g"
    And I should recieve an email with subject "Welcome to WagRegistry."
    And I should recieve an email with subject "Confirm your email address."

  Scenario: A user provides an email already in use.
    Given a "testuser@wagregistry.com" exists
    When I fill in "Email Address" with "testuser@wagregistry.com"
    And I fill in "Name" with "Test User"
    And I fill in "Password" with "shirehobbit"
    And I fill in "Confirm Password" with "shirehobbit"
    And I check "I agree to the terms"
    And I click "Sign Up"
    Then I should be on "/signup"
    And I should see a form error saying "That email address is already in use."
