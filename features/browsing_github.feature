Feature: Browse github repository
  In order to find code in github repository
  As a user
  I need to be able provide phrase and get a list of results

  Scenario: Search with invalid phrase
    Given I want to search for "a" in "github" code
    When I make a request
    Then the response should be "Bad Request"
    And the response should contain:
    """
    {
      "phrase": "Search phrase have to be at least 2 characters long"
    }
    """

  Scenario: Search with invalid "hits per page" and "sort by" query parameters
    Given I want to search for "CompareChecker" in "github" code
    And I set "hitsPerPage" filter to "500"
    And I set "sortBy" filter to "unknown_field"
    When I make a request
    Then the response should be "Bad Request"
    And the response should contain:
    """
    {
      "hitsPerPage": "\"Hits per page\" option have to be a number between 1 and 100",
      "sortBy": "\"Sort by\" option have to be one of the values: score, indexed"
    }
    """

  Scenario: Find code by phrase
    Given I want to search for "CompareChecker" in "github" code
    When I make a request
    Then the response should be "Successful"
    And the response should contain:
    """
      {
        "totalCount": 5,
        "files": [
          {
            "ownerName": "admarc",
            "repositoryName": "playground",
            "fileName": "CompareChecker.php"
          },
          {
            "ownerName": "admarc",
            "repositoryName": "playground",
            "fileName": "CompareChecker.java"
          },
          {
            "ownerName": "admarc",
            "repositoryName": "playground",
            "fileName": "CompareChecker.py"
          },
          {
            "ownerName": "admarc",
            "repositoryName": "playground",
            "fileName": "CompareChecker.cpp"
          },
          {
            "ownerName": "admarc",
            "repositoryName": "playground",
            "fileName": "CompareChecker.json"
          }
        ]
      }
    """
