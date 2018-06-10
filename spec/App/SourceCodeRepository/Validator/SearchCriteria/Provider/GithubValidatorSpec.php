<?php

namespace spec\App\SourceCodeRepository\Validator\SearchCriteria\Provider;

use App\SourceCodeRepository\DTO\SearchCriteria;
use App\SourceCodeRepository\Validator\SearchCriteria\Provider\GithubValidator;
use PhpSpec\ObjectBehavior;

class GithubValidatorSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(GithubValidator::class);
    }

    public function it_should_fail_validation_for_invalid_phrase()
    {
        $searchCriteria = new SearchCriteria('a');
        $this->validate($searchCriteria)->shouldReturn(false);
        $this->getErrors()->shouldReturn(['phrase' => 'Search phrase have to be at least 2 characters long']);
    }

    public function it_should_fail_validation_when_hits_per_page_is_to_big()
    {
        $searchCriteria = new SearchCriteria('comparator', 1, 101);
        $this->validate($searchCriteria)->shouldReturn(false);
        $this->getErrors()->shouldReturn(
            ['hitsPerPage' => '"Hits per page" option have to be a number between 1 and 100']
        );
    }

    public function it_should_fail_validation_when_hits_per_page_is_to_small()
    {
        $searchCriteria = new SearchCriteria('comparator', 1, 0);
        $this->validate($searchCriteria)->shouldReturn(false);
        $this->getErrors()->shouldReturn(
            ['hitsPerPage' => '"Hits per page" option have to be a number between 1 and 100']
        );
    }

    public function it_should_fail_validation_when_sort_by_is_invalid()
    {
        $searchCriteria = new SearchCriteria('comparator', 1, 1, 'unknown');
        $this->validate($searchCriteria)->shouldReturn(false);
        $this->getErrors()->shouldReturn(
            ['sortBy' => '"Sort by" option have to be one of the values: score, indexed']
        );
    }
}
