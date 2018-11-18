Feature: I want to create new message
  Scenario: I need a new message
    Given I am an authenticated user
    When I use api message method "POST" '/messages'
    Then I expect a 201 response code