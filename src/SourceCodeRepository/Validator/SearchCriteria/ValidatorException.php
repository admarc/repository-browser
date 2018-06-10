<?php

namespace App\SourceCodeRepository\Validator\SearchCriteria;

class ValidatorException extends \Exception
{
    private $errors = [];

    public function __construct(array $errors, string $message = 'Search criteria validation failed')
    {
        $this->message = $message;
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
