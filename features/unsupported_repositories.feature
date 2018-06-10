Feature: Browse unsupported repository
  In order to be notified that source code repository is unsupported
  As a user
  I need to get information that picked provider is unsupported

  Scenario: Search with unsupported provider
    Given I want to search for "a" in "unsupported_provider" code
    When I make a request
    Then the response should be "Not Found"
