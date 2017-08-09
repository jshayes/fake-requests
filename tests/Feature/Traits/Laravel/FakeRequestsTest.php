<?php

namespace Tests\Feature\Traits\Laravel;

use GuzzleHttp\Psr7\Response;
use Orchestra\Testbench\TestCase;
use JSHayes\FakeRequests\ClientFactory;
use PHPUnit_Framework_ExpectationFailedException;
use JSHayes\FakeRequests\Traits\Laravel\FakeRequests;

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

    /**
     * @test
     */
    public function a_test_fails_if_not_all_expectations_are_consumed()
    {
        $handler = $this->fakeRequests();
        $handler->get('/test');

        try {
            $this->checkHandler();
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            // Reset the mock handler so that the checkHandler method doesn't fail after this test finishes
            $this->fakeRequests();
            return;
        }

        $this->fail();
    }
}
