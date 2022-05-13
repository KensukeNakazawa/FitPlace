<?php

namespace App\Exceptions;

use Exception;

/**
 * Class ExerciseException
 * @package App\Exceptions
 */
class RateLimitException extends Exception
{
    public $request;
    public $message;

    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}