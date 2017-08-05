<?php

namespace JSHayes\GuzzleTesting\Traits\Laravel;

use JSHayes\GuzzleTesting\MockHandler;
use JSHayes\GuzzleTesting\ClientFactory;

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
