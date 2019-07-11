<?php


namespace App\Service\TermScore;


use App\Service\TermSearch\TermSearchInterface;
use App\Utility\Result\AbstractResult;

interface TermScoreInterface
{
    /**
     * Get calculation score for given term.
     *
     * @see AbstractResult
     * @param string $term Term used to retrieve score.
     * @param TermSearchInterface $search Implementation used for term searching.
     * @return AbstractResult Success containing result as float or Failure containing fail description.
     */
    public function getScore(string $term, TermSearchInterface $search): AbstractResult;
}