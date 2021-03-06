<?php

namespace Tests\Feature\Traits\Laravel;

use GuzzleHttp\Psr7\Response;
use Orchestra\Testbench\TestCase;
use JSHayes\FakeRequests\ClientFactory;
use PHPUnit\Framework\AssertionFailedError;
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
        } catch (AssertionFailedError $e) {
            // Reset the mock handler so that the checkHandler method doesn't fail after this test finishes
            $this->fakeRequests();
            return;
        }

        $this->fail();
    }

    /**
     * @test
     */
    public function a_test_fails_if_not_all_expectations_are_consumed_when_unexpected_api_calls_are_allowed()
    {
        $handler = $this->fakeRequests()->allowUnexpectedCalls();
        $handler->get('/test');

        $client = resolve(ClientFactory::class)->make();
        $client->get('/other');

        try {
            $this->checkHandler();
        } catch (AssertionFailedError $e) {
            // Reset the mock handler so that the checkHandler method doesn't fail after this test finishes
            $this->fakeRequests();
            return;
        }

        $this->fail();
    }
}
