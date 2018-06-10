<?php

namespace spec\App\Interactor;

use App\Entity\Files;
use App\Interactor\FindFiles;
use App\Interactor\UnsupportedProviderException;
use App\SourceCodeRepository\DTO\SearchCriteria;
use App\SourceCodeRepository\Provider\ProviderInterface;
use App\SourceCodeRepository\Validator\SearchCriteria\ValidatorException;
use App\SourceCodeRepository\Validator\SearchCriteria\ValidatorInterface;
use PhpSpec\ObjectBehavior;

class FindFilesSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(FindFiles::class);
    }

    public function it_should_fail_for_unknown_provider()
    {
        $this->shouldThrow(UnsupportedProviderException::class)->duringExecute(
            'unknown_provider',
            new SearchCriteria('phrase')
        );
    }

    public function it_should_fail_when_search_criteria_is_invalid(
        ProviderInterface $provider,
        ValidatorInterface $searchCriteriaValidator
    ) {
        $searchCriteria = new SearchCriteria('phrase');

        $this->addProvider($provider, 'github');
        $this->addValidator($searchCriteriaValidator, 'github');

        $searchCriteriaValidator->validate($searchCriteria)->shouldBeCalled()->willReturn(false);
        $searchCriteriaValidator->getErrors()->shouldBeCalled()->willReturn([]);

        $this->shouldThrow(ValidatorException::class)->duringExecute(
            'github',
            new SearchCriteria('phrase')
        );
    }

    public function it_should_call_search_on_selected_provider_when_search_criteria_is_valid(
        ProviderInterface $provider,
        ValidatorInterface $searchCriteriaValidator
    ) {
        $searchCriteria = new SearchCriteria('phrase');

        $this->addProvider($provider, 'github');
        $this->addValidator($searchCriteriaValidator, 'github');

        $searchCriteriaValidator->validate($searchCriteria)->shouldBeCalled()->willReturn(true);

        $files = new Files(1);
        $provider->findFiles($searchCriteria)->shouldBeCalled()->willReturn($files);

        $this->execute('github', $searchCriteria)->shouldReturn($files);
    }

    public function it_should_call_search_on_selected_provider_when_no_validator_is_added(ProviderInterface $provider)
    {
        $this->addProvider($provider, 'github');

        $searchCriteria = new SearchCriteria('phrase', 1, 100, 'score');
        $files = new Files(1);

        $provider->findFiles($searchCriteria)->shouldBeCalled()->willReturn($files);

        $this->execute('github', $searchCriteria)->shouldReturn($files);
    }
}
