<?php


namespace App\Utility;

use App\Entity\TermSearchResult;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TermSearchResultCacheValidation implements CacheValidationInterface
{
    private $dateTimeFormat;

    public function __construct(string $dateTimeFormat)
    {
        $this->dateTimeFormat = $dateTimeFormat;
    }

    /**
     * @inheritDoc
     */
    public function isValid($element): bool
    {
        $cacheTimeLimit = new \DateTime($this->dateTimeFormat);

        return $element->getUpdated() >= $cacheTimeLimit;
    }
}