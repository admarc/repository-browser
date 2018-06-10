Repository browser
==================
Example application which search in GitHub code.

Setting up
----------
Provide environment variable GITHUB_OAUTH_TOKEN which will be used to search in GitHub code.
Read more on: https://developer.github.com/v3/

Api documentation
-----------------
Api documentation is available at http://your_host/api/doc (only in dev environment)

![Api](/doc/api_swagger.png)


Running the application
-------------
* Install all dependencies (you have to have composer installed):
```
composer install
```

* Run Symfony built-in Web Server (only for development and testing environments):
```
bin/console server:start
```

* Example usage:

http://your_host/api/v1/repositories/github/files?phrase=CompareChecker (remember that in *test* environment the search is performed on mocked GitHub api)

Quality check
-------------
Application was created using Behavior-driven development (**BDD**) approach.

**Behat** and **phpspec** were used to test business logic.

**PHP CS Fixer**,**PHP Mess Detector**,**SensioLabs Security Checker** were use to test code quality.

To run all quality checks and tests:
```
ant quality-check
```
Behat scenarios will only work properly for test environment (due to mocked GitHub client service).

Extending
---------
To add support for other source code repository (like Bitbucket or GitLab):

- create provider which implements **App\SourceCodeRepository\Provider\ProviderInterface**
- tag provider as "app.source_code_provider" (it will be automatically available to use in file search interactor), example:
```
    app.source_code_repository.provider.bitbucket:
        class: App\SourceCodeRepository\Provider\Bitbucket
        tags:
            - { name: app.source_code_provider, alias: bitbucket }
```
- (optional) add search criteria validator for provider (it will be also automatically used by file search interactor), example:
```
    app.source_code_repository.validator.search_criteria.provider.github_validator:
        class: App\SourceCodeRepository\Validator\SearchCriteria\Provider\BitbucketValidator
        tags:
            - { name: app.search_criteria_validator, alias: bitbucket }
```
