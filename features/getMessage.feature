Feature: Use Get method Messages
  Scenario: I want to get list of messages
    Given I am an authenticated user
    When I use api message method "GET" 'v1/messages'
    Then I expect a 200 response code
    And I expect at least 1 result