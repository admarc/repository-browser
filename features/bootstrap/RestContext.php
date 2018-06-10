<?php

use Behat\Behat\Context\Context;
use GuzzleHttp\Client;
use Behat\Gherkin\Node\PyStringNode;

class RestContext implements Context
{
    private $method;
    private $url;
    private $filters = [];
    private $response;
    private $client;
    private $apiVersion = 'v1';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @Given I want to search for :phrase in :repository code
     */
    public function iWantToSearchForInCode(string $phrase, string $repository)
    {
        $this->url = sprintf('repositories/%s/files?phrase=%s', $repository, $phrase);
        $this->method = 'get';
    }

    /**
     * @When I make a request
     */
    public function iMakeARequest()
    {
        $url = sprintf('/api/%s/%s', $this->apiVersion, $this->url);

        if (!empty($this->filters)) {
            $url = sprintf('%s&%s', $url, http_build_query($this->filters));
        }

        $this->response = $this->client->request(
            $this->method,
            $url
        );
    }

    /**
     * @Then the response should be :status
     */
    public function theResponseStatusShouldBe(string $status)
    {
        $statusesMap = [
            'Successful' => 200,
            'Bad Request' => 400,
            'Not Found' => 404,
        ];

        $responseStatus = $this->response->getStatusCode();

        if ($responseStatus !== $statusesMap[$status]) {
            throw new \RestResponseException(
                sprintf('Given response status does not match actual one: %s', $responseStatus)
            );
        }
    }

    /**
     * @Then the response should contain:
     */
    public function theResponseShouldContain(PyStringNode $response)
    {
        $rawResponse = $this->cleanUpJsonResponse($response->getRaw());

        $responseContent = $this->response->getBody()->getContents();

        if ($rawResponse !== $responseContent) {
            throw new \RestResponseException(
                sprintf('Given response does not match actual one: %s', print_r($responseContent, true))
            );
        }
    }

    private function cleanUpJsonResponse(string $json): string
    {
        return json_encode(json_decode($json));
    }

    /**
     * @Given I set :name filter to :value
     */
    public function iSetFilterTo(string $name, string $value)
    {
        $this->filters[$name] = $value;
    }
}
