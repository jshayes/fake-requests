<?php

namespace JSHayes\FakeRequests\Exceptions;

use Exception;
use Psr\Http\Message\RequestInterface;

class UnhandledRequestException extends Exception
{
    public function __construct(RequestInterface $request)
    {
        $method = strtoupper($request->getMethod());

        parent::__construct("There was no response defined for the {$method} request to \"{$request->getUri()}\".");
    }
}
