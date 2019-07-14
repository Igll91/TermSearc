<?php


namespace App\Utility\Result;

/**
 * Class AbstractResult
 *
 * <pre>
 * Purpose of this class abstraction is to simplify return
 * type for functions which can be executed successfully or not,
 * without throwing exceptions.
 *
 * AbstractResult class can either be instance of Success or Failure.
 * </pre>
 *
 * @see Success
 * @see Failure
 * @package App\Utility\Result
 */
abstract class AbstractResult
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    abstract public function isSuccessful(): bool;

    public function get()
    {
        return $this->value;
    }
}
