<?php


namespace App\Tests\Service\TermScore;


use App\Service\TermScore\UndabotTermScore;
use App\Service\TermSearch\Github\GithubTermSearch;
use App\Utility\Result\Failure;
use App\Utility\Result\Success;
use PHPUnit\Framework\TestCase;

class UndabotTermScoreTest extends TestCase
{
    private const TERM = "Test";
    private const RESULT_FAILURE_STRING = "Expected behavior reached!";

    /**
     * Test getScore function with mocked successful GITHUB API results.
     */
    public function testGetScoreSuccess() {
        $positiveValue = 5000;
        $negativeValue = 7000;
        $this->executeGetScoreSuccessfulTest($positiveValue, $negativeValue, $this->calculateScore($positiveValue, $negativeValue));
    }

    /**
     * Test getScore function with maximum result mocked successful GITHUB API results.
     */
    public function testGetScoreSuccessMax() {
        $this->executeGetScoreSuccessfulTest(12000, 0, UndabotTermScore::MAX_VALUE_REPRESENTATION);
    }

    /**
     * Test getScore function with minimum result mocked successful GITHUB API results.
     */
    public function testGetScoreSuccessMin() {
        $this->executeGetScoreSuccessfulTest(0, 7000, 0);
    }

    /**
     * * Test getScore function with mocked one successful and one unsuccessful GITHUB API result.
     */
    public function testGetScoreFailure() {
        $undabotTermScore = new UndabotTermScore();
        $mock = $this->getGithubTermSearchMock([$this, 'getTermCountFailureCallback']);

        $result         = $undabotTermScore->getScore(self::TERM, $mock);
        $wantedResult   = new Failure(self::RESULT_FAILURE_STRING);

        $this->assertEquals($wantedResult, $result);
    }

    /**
     * Execute getScore UT with successful API results and given values.
     *
     * @param $positiveValue float Positive term value that will be returned by API.
     * @param $negativeValue float Negative term value that will be returned by API.
     * @param $wantedValue float Wanted result value that is asserted with function result.
     */
    private function executeGetScoreSuccessfulTest($positiveValue, $negativeValue, $wantedValue) {
        $undabotTermScore = new UndabotTermScore();

        $mock = $this->getGithubTermSearchMock(
            function($term) use ($positiveValue, $negativeValue){
                return forward_static_call_array([$this, 'getTermCountSuccessfulCallback'], [$term, $positiveValue, $negativeValue]);
            });

        $result         = $undabotTermScore->getScore(self::TERM, $mock);
        $wantedResult   = new Success($wantedValue);

        $this->assertEquals($wantedResult, $result);
    }

    /**
     * Mock getTermCount function call return values to return two successful API search results.
     *
     * @param $searchTerm string Search term.
     * @param float $positiveValue Positive term value that will be returned by API.
     * @param float $negativeValue Negative term value that will be returned by API.
     * @return Failure|Success
     */
    public function getTermCountSuccessfulCallback(string $searchTerm, float $positiveValue, float $negativeValue) {
        switch ($searchTerm) {
            case self::TERM . " " . UndabotTermScore::POSITIVE_TERM_APPENDIX:
                return new Success($positiveValue);
            case self::TERM . " " . UndabotTermScore::NEGATIVE_TERM_APPENDIX:
                return new Success($negativeValue);
            default:
                return new Failure("UT setup failure");
        }
    }

    /**
     * Mock getTermCount function call return values to return one failed API search results.
     *
     * @param $searchTerm string Search term.
     * @return Failure|Success
     */
    public function getTermCountFailureCallback(string $searchTerm) {
        switch ($searchTerm) {
            case self::TERM . " " . UndabotTermScore::POSITIVE_TERM_APPENDIX:
                return new Success(5000);
            case self::TERM . " " . UndabotTermScore::NEGATIVE_TERM_APPENDIX:
                return new Failure(self::RESULT_FAILURE_STRING);
            default:
                return new Failure("UT setup failure");
        }
    }

    /**
     * Mock GithubTermSearch class with given callback.
     *
     * @param callable $getTermCountCallback Callback function for mocking result of getTermCount function.
     * @return \PHPUnit\Framework\MockObject\MockObject Mocked object.
     */
    public function getGithubTermSearchMock(callable $getTermCountCallback) {
        $stub = $this->getMockBuilder(GithubTermSearch::class)
            ->disableOriginalConstructor()
            ->getMock();

        $stub->expects($this->exactly(2))    // Service function does 2 API calls
            ->method('getTermCount')
            ->will($this->returnCallback($getTermCountCallback));

        return $stub;
    }

    /**
     * Get score for given positive and negative value.
     *
     * Used to validate UndabotTermScore getScore algorithm changes.
     * Note this UT function should change only if algorithm calculation changes.
     *
     * @param $positiveValue float Positive term value that was returned by API.
     * @param $negativeValue float Negative term value that was returned by API.
     * @return float Calculated score from given positive and negative value.
     */
    private function calculateScore($positiveValue, $negativeValue): float {
        return $positiveValue / ($negativeValue + $positiveValue) * UndabotTermScore::MAX_VALUE_REPRESENTATION;
    }
}