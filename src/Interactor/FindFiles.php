<?php

namespace App\Interactor;

use App\Entity\Files;
use App\SourceCodeRepository\DTO\SearchCriteria;
use App\SourceCodeRepository\Provider\ProviderException;
use App\SourceCodeRepository\Provider\ProviderInterface;
use App\SourceCodeRepository\Validator\SearchCriteria\ValidatorException;
use App\SourceCodeRepository\Validator\SearchCriteria\ValidatorInterface;

class FindFiles
{
    private $providers = [];
    private $validators = [];

    /**
     * @throws ValidatorException
     * @throws ProviderException
     * @throws UnsupportedProviderException
     */
    public function execute(string $provider, SearchCriteria $searchCriteria): Files
    {
        if (!isset($this->providers[$provider])) {
            throw new UnsupportedProviderException(sprintf('Unknown provider: %s', $provider));
        }

        if (isset($this->validators[$provider])) {
            $validator = $this->validators[$provider];
            if (!$validator->validate($searchCriteria)) {
                throw new ValidatorException($validator->getErrors());
            }
        }

        return $this->providers[$provider]->findFiles($searchCriteria);
    }

    public function addProvider(ProviderInterface $provider, string $alias)
    {
        $this->providers[$alias] = $provider;
    }

    public function addValidator(ValidatorInterface $criteriaValidator, string $alias)
    {
        $this->validators[$alias] = $criteriaValidator;
    }
}
