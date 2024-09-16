@javascript
Feature: View Tickets
  In order to view my support tickets,
  As a logged in user,
  I must visit /account/tickets and click on the ticket.

  Background:
    Given a test user exists
    And I am logged in as "testuser@wagregistry.com" with password "shirehobbit"

  Scenario: A user views a support ticket.
    When I go to "/account/tickets"
    And I click "Dummy Ticket"
    Then I should be on "/account/ticket/1"

  Scenario: A user attempts to view an unowned support ticket.
    Given a "set of random users" exists
    When a go to "/account/ticket/11"
    Then a should be on "/404"
