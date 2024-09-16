@javascript
Feature: Message Pagination
  In order to view every message contained in a long support ticket,
  As the logged in owner of the ticket,
  I must follow the pagination links.

  Background:
    Given A test user exists
    And I am logged in as "testuser@wagregistry.com" with password "shirehobbit"
    And I am on "/account/ticket/1"

  Scenario: Messages are paginated when a ticket contains more than 10.
    Then I should see a ".pagination" element

  Scenario Outline: An iterator should keep track of a users place across pagination.
    When I click ".page<page>"
    Then I should see "Viewing messages <iterator> total messages." in the ".iterator" element
    Examples:
      | page | iterator              |
      | 1    | "1 through 10 of 25"  |
      | 2    | "11 through 20 of 25" |
      | 3    | "21 through 25 of 25" |
