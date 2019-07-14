<?php


namespace App\Utility;

interface CacheValidationInterface
{
    /**
     * Check if cache is still valid for given element.
     *
     * @param $element mixed Element to check if cache is valid.
     * @return bool True if cache is valid, false otherwise.
     */
    public function isValid($element): bool;
}
