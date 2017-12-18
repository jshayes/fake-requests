<?php

namespace JSHayes\FakeRequests\Exceptions;

use Exception;
use Psr\Http\Message\RequestInterface;

class UnhandledRequestException extends Exception
{
    public function __construct(RequestInterface $request)
    {
        $method = strtoupper($request->getMethod());
        $path = ltrim($request->getUri()->getPath(), '/');

        parent::__construct(sprintf('There was no response defined for the %s request to %s.', $method, $path));
    }
}
