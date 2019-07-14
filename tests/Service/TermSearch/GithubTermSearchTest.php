<?php


namespace App\Tests\Service\TermSearch;

use App\Service\TermSearch\Github\GithubTermSearch;
use App\Service\TermSearch\Github\GithubTermSearchBuilder;
use App\Utility\Result\Failure;
use PHPUnit\Framework\TestCase;

class GithubTermSearchTest extends TestCase
{
    // limitation from https://api.github.com/rate_limit
    private const GITHUB_API_LIMIT = 10;

    /**
     * Validate timeout exception will return Failure object.
     */
    public function testTimeout() {
        $githubTermSearchBuilder    = new GithubTermSearchBuilder();
        $githubTermSearchBuilder->setTimeout(0.1);

        $result = $this->getSearchResult("symfony", $githubTermSearchBuilder);

        $this->assertEquals(new Failure("Connection exception, please try again."), $result);
    }

    /**
     * Validate successful API call.
     */
    public function testSuccess() {
        $result = $this->getSearchResult("symfony", new GithubTermSearchBuilder());

        $this->assertTrue($result->isSuccessful());
    }

    /**
     * Validate invalid size query will return Failure object.
     */
    public function testQueryLength() {
        $searchTerm = "VQCFumEfKvWZFJYJQFQcjVTVeqUgpeeepupvAYLkBSeDGncYPMrvpmbriPqAjLZFCxRVkYmcSjNWiZHdaaccCUJTaBcgukeDdReRnDTByHPVAGvLzueYGJkaJxPBgGucUamVADjfVVawAjXeSWKwfwaCKjPVaFrekaJxPBgGucUamVADjfVVawAjXeSWKwfwaCKjPVaFrecuHJRznpUGexvUDmuEtMBwMEdkRzSjkxtNAxfhGfpuAPXEFFbwYyPQSerHGzFJDdcuHJRznpUGexvUDmuEtMBwMEdkRzSjkxtNAxfhGfpuAPXEFFbwYyPQSerHGzFJDdKc";
        $result     = $this->getSearchResult($searchTerm, new GithubTermSearchBuilder());

        $this->assertEquals(new Failure("Unprocessable Entity"), $result);
    }

    /**
     * Validate invalid size query will return Failure object.
     */
    public function testLimitation() {
        $searchTerm                 = "symfony";
        $githubTermSearchBuilder    = new GithubTermSearchBuilder();
        $githubTermSearch           = new GithubTermSearch($githubTermSearchBuilder);

        for($i = 0; $i < self::GITHUB_API_LIMIT; $i++) {
            $githubTermSearch->getTermCount($searchTerm);
        }

        $result = $githubTermSearch->getTermCount($searchTerm);

        $this->assertEquals(new Failure("Forbidden"), $result);
    }

    private function getSearchResult(string $searchTerm, GithubTermSearchBuilder $githubTermSearchBuilder) {
        $githubTermSearch = $githubTermSearchBuilder->build();

        return $githubTermSearch->getTermCount($searchTerm);
    }
}