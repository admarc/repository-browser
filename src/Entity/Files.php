<?php

namespace App\Entity;

class Files
{
    private $totalCount;
    private $files = [];

    public function __construct(int $totalCount)
    {
        $this->totalCount = $totalCount;
    }

    public function addFile(File $file)
    {
        $this->files[] = $file;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
