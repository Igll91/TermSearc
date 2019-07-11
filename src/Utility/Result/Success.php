<?php


namespace App\Utility\Result;


class Success extends AbstractResult
{
    public function isSuccessful(): bool
    {
        return true;
    }
}