<?php

namespace JSHayes\GuzzleTesting\Exceptions;

use Exception;

class UnhandledRequestException extends Exception
{
    public function __construct(string $method, string $path)
    {
        parent::__construct(sprintf('There was no response defined for the %s request to %s.', $method, $path));
    }
}
