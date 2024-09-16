@javascript @cache
Feature: Authentication Ban
  In order to be banned from authenticating for 1 hour,
  As a logged out user,
  I must fail to login or signup 5 times within an hour.

  Background:
    Given I am on "/login"

  Scenario: I fail to login 3 times.
    When I fail to login "3" times
    Then I should be on "/login"
    And I should see a "warning" notification saying "You have failed to login 3 times. You have 2 remaining attempts before a temporary one hour ban is implemented."

  Scenario: I fail to login 5 times.
    When I fail to login "5" times
    Then I should be on "/"
    And I should see a "danger" notification saying "You have attempted to authenticate too many times. Try again in 60 minutes."
