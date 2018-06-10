<?php

namespace App\SourceCodeRepository\Provider;

use App\Entity\Files;
use App\SourceCodeRepository\DTO\SearchCriteria;

interface ProviderInterface
{
    public function findFiles(SearchCriteria $searchCriteria): Files;
}
