<?php


namespace App\Utility\Result;


class Failure extends AbstractResult
{
    public function isSuccessful(): bool
    {
        return false;
    }
}