<?php


namespace App\Service\TermScore;


use App\Service\TermSearch\TermSearchInterface;
use App\Utility\Result\AbstractResult;
use App\Utility\Result\Success;

/**
 * Class UndabotTermScore
 *
 * @see TermScoreInterface
 * @package App\Service\TermScore
 */
class UndabotTermScore implements TermScoreInterface
{
    public const POSITIVE_TERM_APPENDIX = "rocks";
    public const NEGATIVE_TERM_APPENDIX = "sucks";

    public const MAX_VALUE_REPRESENTATION = 10;

    /**
     * Get calculation score for given term.
     *
     * <pre>
     * Score is calculated from two search queries which are
     * concatenated from given term and appendix 'rocks' for
     * positive result and appendix 'sucks' for negative result.
     *
     * Result is displayed in range from 0 to 10 as a
     * ratio of the positive result and the total number of results.
     * </pre>
     *
     * @param string $term Term used to retrieve score.
     * @param TermSearchInterface $search Implementation used for term searching.
     * @return AbstractResult Success containing result as float or Failure containing fail description.
     */
    public function getScore(string $term, TermSearchInterface $search): AbstractResult
    {
        $appendixes = [self::POSITIVE_TERM_APPENDIX => 0, self::NEGATIVE_TERM_APPENDIX => 0];

        foreach ($appendixes as $key => &$value) {
            $result = $search->getTermCount($term . " " . $key);

            if(!$result->isSuccessful()) {
                return $result;
            }

            $value = $result->get();
        }

        $positiveResult = (float) $appendixes[self::POSITIVE_TERM_APPENDIX];
        $totalResult    = $positiveResult + (float)$appendixes[self::NEGATIVE_TERM_APPENDIX];

        return new Success($positiveResult / $totalResult * self::MAX_VALUE_REPRESENTATION);
    }
}