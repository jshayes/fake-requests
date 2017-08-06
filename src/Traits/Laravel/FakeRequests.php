<?php

namespace JSHayes\FakeRequests\Traits\Laravel;

use JSHayes\FakeRequests\MockHandler;
use JSHayes\FakeRequests\ClientFactory;

trait FakeRequests
{
    protected function fakeRequests(): MockHandler
    {
        $factory = new ClientFactory();
        $factory->setHandler($mockHandler = new MockHandler());
        app()->instance(ClientFactory::class, $factory);
        return $mockHandler;
    }
}
