<?php


namespace App\Service\TermSearch;


use App\Utility\Result\AbstractResult;

interface TermSearchInterface
{
    /**
     * Get number of found matches for given term.
     *
     * @see AbstractResult
     * @param string $term Term to be searched.
     * @return AbstractResult Success containing result as float or Failure containing fail description.
     */
    public function getTermCount(string $term) : AbstractResult;
}