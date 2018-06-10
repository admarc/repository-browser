<?php

namespace App\SourceCodeRepository\DTO;

class SearchCriteria
{
    private $phrase;
    private $page;
    private $hitsPerPage;
    private $sortBy;

    public function __construct(
        string $phrase,
        int $page = 1,
        int $hitsPerPage = 25,
        string $sortBy = 'score'
    ) {
        $this->phrase = $phrase;
        $this->page = $page;
        $this->hitsPerPage = $hitsPerPage;
        $this->sortBy = $sortBy;
    }

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getHitsPerPage(): int
    {
        return $this->hitsPerPage;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }
}
