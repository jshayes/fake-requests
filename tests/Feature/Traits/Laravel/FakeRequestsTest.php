<?php

namespace Tests\Feature\Traits\Laravel;

use GuzzleHttp\Psr7\Response;
use Orchestra\Testbench\TestCase;
use JSHayes\GuzzleTesting\ClientFactory;
use JSHayes\GuzzleTesting\Traits\Laravel\FakeRequests;

class FakeRequestsTest extends TestCase
{
    use FakeRequests;

    /**
     * @test
     */
    public function fake_requests_uses_mock_handler_for_clients_created_with_the_factory()
    {
        $handler = $this->fakeRequests();
        $handler->get('/test')->respondWith($response = new Response());
        $client = resolve(ClientFactory::class)->make();
        $this->assertSame($response, $client->get('/test'));
    }
}
