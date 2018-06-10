<?php

namespace spec\App\SourceCodeRepository\Provider;

use App\Entity\File;
use App\Entity\Files;
use App\SourceCodeRepository\DTO\SearchCriteria;
use App\SourceCodeRepository\Provider\GitHub;
use App\SourceCodeRepository\Provider\ProviderException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GitHubSpec extends ObjectBehavior
{
    private $client;
    private $clientOptions = ['headers' => ['Authorization' => 'token secret_oauth_token']];

    public function let(ClientInterface $client)
    {
        $this->beConstructedWith($client, 'secret_oauth_token');
        $this->client = $client;
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(GitHub::class);
    }

    public function it_should_call_github_api_for_results(
        ResponseInterface $response,
        StreamInterface $stream
    ) {
        $files = new Files(2);
        $file1 = new File('admarc', 'playground', 'CompareCheckerTest.java');
        $file2 = new File('admarc', 'playground', 'CompareCheckerTest.php');
        $files->addFile($file1);
        $files->addFile($file2);

        $this->client->request(
            'GET',
            'code?q=CompareChecker+in:file&page=1&per_page=50&sort=indexed',
            $this->clientOptions
        )
            ->shouldBeCalled()
            ->willReturn($response);

        $response->getBody()->shouldBeCalled()->willReturn($stream);
        $stream->getContents()->shouldBeCalled()->willReturn(file_get_contents(
            sprintf('%s/Responses/%s', __DIR__, 'github_code_search.json')
        ));

        $searchCriteria = new SearchCriteria('CompareChecker', 1, 50, 'indexed');

        $this->findFiles($searchCriteria)->shouldBeLike($files);
    }

    public function it_should_fail_when_github_api_fail(
    ) {
        $options = ['headers' => ['Authorization' => 'token secret_oauth_token']];

        $this->client->request(
            'GET',
            'code?q=CompareChecker+in:file&page=1&per_page=25&sort=score',
            $this->clientOptions
        )
            ->shouldBeCalled()
            ->willThrow(TransferException::class);

        $this->shouldThrow(new ProviderException('GitHub api problem occurred.'))->duringFindFiles(
            new SearchCriteria('CompareChecker')
        );
    }
}
