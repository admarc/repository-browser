<?php

namespace App\Test\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

class GithubMockedClient implements ClientInterface
{
    /**
     * @SuppressWarnings("unused")
     */
    public function request($method, $uri, array $options = [])
    {
        return new Response(200, [], file_get_contents(sprintf('%s/%s', __DIR__, 'github_code_result.json')));
    }

    /**
     * @SuppressWarnings("unused")
     */
    public function send(RequestInterface $request, array $options = [])
    {
    }

    /**
     * @SuppressWarnings("unused")
     */
    public function sendAsync(RequestInterface $request, array $options = [])
    {
    }

    /**
     * @SuppressWarnings("unused")
     */
    public function requestAsync($method, $uri, array $options = [])
    {
    }

    /**
     * @SuppressWarnings("unused")
     */
    public function getConfig($option = null)
    {
    }
}
