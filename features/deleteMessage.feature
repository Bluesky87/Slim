Feature: I want to delete  message
  Scenario: I need to delete message
    Given I am an authenticated user
    When I use api message method "DELETE" '/messages/1'
    Then I expect a 204 response code