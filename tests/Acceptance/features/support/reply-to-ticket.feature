@javascript
Feature: Reply To Ticket
  In order to reply to a ticket,
  As the logged in owner of the ticket,
  I must enter my reply into the form on the ticket page.

  Scenario: A user replies to a ticket.
    Given A test user exists
    And I am logged in as "testuser@wagregistry.com" with password "shirehobbit"
    And I am on "/account/ticket/1"
    When I fill in "Message" with "Thank you for your reply."
    And I click "Submit Reply"
    Then I should see a ".new-message" element
    And I should recieve an email with subject "Your message to support."
