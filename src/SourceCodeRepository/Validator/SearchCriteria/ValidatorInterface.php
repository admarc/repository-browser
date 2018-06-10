<?php

namespace App\SourceCodeRepository\Validator\SearchCriteria;

use App\SourceCodeRepository\DTO\SearchCriteria;

interface ValidatorInterface
{
    public function validate(SearchCriteria $searchCriteria): bool;

    public function getErrors(): array;
}
