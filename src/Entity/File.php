<?php

namespace App\Entity;

class File
{
    private $ownerName;
    private $repositoryName;
    private $fileName;

    public function __construct(string $ownerName, string $repositoryName, string $fileName)
    {
        $this->ownerName = $ownerName;
        $this->repositoryName = $repositoryName;
        $this->fileName = $fileName;
    }

    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    public function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
