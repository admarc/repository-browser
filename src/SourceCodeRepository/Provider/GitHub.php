<?php

namespace App\SourceCodeRepository\Provider;

use App\Entity\File;
use App\Entity\Files;
use App\SourceCodeRepository\DTO\SearchCriteria;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class GitHub implements ProviderInterface
{
    private $client;
    private $authenticationToken;

    public function __construct(
        ClientInterface $client,
        string $authenticationToken
    ) {
        $this->client = $client;
        $this->authenticationToken = $authenticationToken;
    }

    /**
     * @throws ProviderException
     */
    public function findFiles(SearchCriteria $searchCriteria): Files
    {
        $this->formatUri($searchCriteria);

        $options = ['headers' => ['Authorization' => sprintf('%s %s', 'token', $this->authenticationToken)]];

        try {
            $response = $this->client->request('GET', $this->formatUri($searchCriteria), $options);
        } catch (GuzzleException $exception) {
            throw new ProviderException('GitHub api problem occurred.', 0, $exception);
        }

        $results = json_decode($response->getBody()->getContents(), true);

        return $this->mapResults($results);
    }

    private function formatUri(SearchCriteria $searchCriteria): string
    {
        $formattedUri = sprintf(
            'code?q=%s+in:file&page=%d&per_page=%d&sort=%s',
            $searchCriteria->getPhrase(),
            $searchCriteria->getPage(),
            $searchCriteria->getHitsPerPage(),
            $searchCriteria->getSortBy()
        );

        return $formattedUri;
    }

    private function mapResults(array $results): Files
    {
        $files = new Files($results['total_count']);
        foreach ($results['items'] as $item) {
            $file = new File($item['repository']['owner']['login'], $item['repository']['name'], $item['name']);
            $files->addFile($file);
        }

        return $files;
    }
}
