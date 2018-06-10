<?php

namespace App\Controller;

use App\Interactor\FindFiles;
use App\Interactor\UnsupportedProviderException;
use App\SourceCodeRepository\DTO\SearchCriteria;
use App\SourceCodeRepository\Provider\ProviderException;
use App\SourceCodeRepository\Validator\SearchCriteria\ValidatorException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;

class RepositoryController
{
    private $findFilesInteractor;
    private $serializer;
    private $logger;

    public function __construct(
        FindFiles $findFilesInteractor,
        SerializerInterface $serializer,
        LoggerInterface $logger
    ) {
        $this->findFilesInteractor = $findFilesInteractor;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    /**
     * @Operation(
     *     summary="Search source code repositories for given phrase",
     *     @SWG\Parameter(
     *         name="provider", in="path", required=true, default="github", type="string",
     *         description="Source code provider. Only github is supported for now."
     *     ),
     *     @SWG\Parameter(
     *         name="phrase", in="query", required=true, type="string",
     *         description="Search phrase"
     *     ),
     *     @SWG\Parameter(
     *         name="page", in="query", required=false, type="integer",
     *         description="Page number"
     *     ),
     *     @SWG\Parameter(
     *         name="hitsPerPage", in="query", required=false, type="integer",
     *         description="Number of hits per page"
     *     ),
     *     @SWG\Parameter(
     *         name="sortBy", in="query", required=false, type="string",
     *         description="Sort by field"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful.",
     *          @Model(type=App\Entity\Files::class)
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Returned when search criteria is invalid."
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Returned when the source code repository provider is not found."
     *     ),
     *     @SWG\Response(
     *         response="503",
     *         description="Returned when there is a problem with the source code repository"
     *     )
     * )
     */
    public function filesAction(Request $request, string $provider)
    {
        $searchCriteria = $this->buildSearchCriteria($request);

        try {
            $files = $this->findFilesInteractor->execute($provider, $searchCriteria);
        } catch (ValidatorException $exception) {
            return $this->createJsonResponse(Response::HTTP_BAD_REQUEST, json_encode($exception->getErrors()));
        } catch (UnsupportedProviderException $exception) {
            return $this->createJsonResponse(Response::HTTP_NOT_FOUND);
        } catch (ProviderException $exception) {
            $this->logger->error($exception->getMessage(), $exception->getTrace());

            return $this->createJsonResponse(Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $response = $this->serializer->serialize($files, 'json');

        return JsonResponse::fromJsonString($response);
    }

    private function buildSearchCriteria(Request $request): SearchCriteria
    {
        $phrase = $request->query->get('phrase', '');
        $page = $request->query->get('page', 1);
        $hitsPerPage = $request->query->get('hitsPerPage', 25);
        $sortBy = $request->query->get('sortBy', 'score');

        $searchCriteria = new SearchCriteria($phrase, (int) $page, (int) $hitsPerPage, $sortBy);

        return $searchCriteria;
    }

    private function createJsonResponse(int $status, string $body = ''): Response
    {
        $response = new Response($body, $status, ['content-type' => 'application/json']);

        return $response;
    }
}
