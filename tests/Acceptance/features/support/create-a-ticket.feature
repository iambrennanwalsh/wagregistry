@javascript
Feature: Create a Ticket
  In order to contact support,
  As a logged in user,
  I must use the form on the Support route.

  Scenario: A user opens a new support ticket.
    Given A test user exists
    And I am logged in as "testuser@wagregistry.com" with password "shirehobbit"
    And I am on "/support"
    When I fill in "Subject" with "Issues with my account."
    And I fill in "Message" with "I'm having issues logging into my account."
    And I click "Create Ticket"
    Then I should be on "/account/tickets/1"
    And I should recieve an email with subject "Your inquiry to WagRegistry support."
