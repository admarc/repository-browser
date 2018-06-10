<?php

namespace App\SourceCodeRepository\Validator\SearchCriteria\Provider;

use App\SourceCodeRepository\DTO\SearchCriteria;
use App\SourceCodeRepository\Validator\SearchCriteria\ValidatorInterface;

class GithubValidator implements ValidatorInterface
{
    private $errors = [];
    private $isValid = true;

    public function validate(SearchCriteria $searchCriteria): bool
    {
        if (!$this->isStringLengthGreaterThan($searchCriteria->getPhrase(), 1)) {
            $this->addError('phrase', 'Search phrase have to be at least 2 characters long');
        }

        if (!$this->isIntInRange($searchCriteria->getHitsPerPage(), 1, 100)) {
            $this->addError('hitsPerPage', '"Hits per page" option have to be a number between 1 and 100');
        }

        $sortOptions = ['score', 'indexed'];

        if (!in_array($searchCriteria->getSortBy(), $sortOptions)) {
            $this->addError(
                'sortBy',
                sprintf('"Sort by" option have to be one of the values: %s', implode(', ', $sortOptions))
            );
        }

        return $this->isValid;
    }

    private function isStringLengthGreaterThan(string $subject, $limit): bool
    {
        return strlen($subject) > $limit;
    }

    private function isIntInRange(int $subject, int $min, int $max): bool
    {
        return $subject >= $min && $subject <= $max;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function addError(string $field, string $message)
    {
        $this->errors[$field] = $message;
        $this->isValid = false;
    }
}
