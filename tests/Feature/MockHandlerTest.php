<?php

namespace Tests\Feature;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use JSHayes\FakeRequests\MockHandler;
use Psr\Http\Message\RequestInterface;
use JSHayes\FakeRequests\ClientFactory;
use JSHayes\FakeRequests\Exceptions\UnhandledRequestException;

class MockHandlerTest extends TestCase
{
    private function makeClient(MockHandler $handler, array $options = [])
    {
        $factory = new ClientFactory();
        $factory->setHandler($handler);
        return $factory->make($options);
    }

    /**
     * @test
     */
    public function it_throws_unhandled_request_exception_when_no_expectations_have_been_set()
    {
        $this->expectException(UnhandledRequestException::class);
        $this->makeClient(new MockHandler())->get('/test');
    }

    /**
     * @test
     */
    public function it_throws_unhandled_request_exception_when_there_is_an_expectation_for_the_path_but_with_a_different_method()
    {
        $this->expectException(UnhandledRequestException::class);
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->post('/test');
        $client->get('/test');
    }

    /**
     * @test
     */
    public function it_throws_unhandled_request_exception_when_there_is_an_expectation_for_the_method_but_with_a_different_path()
    {
        $this->expectException(UnhandledRequestException::class);
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->post('/tests');
        $client->post('/test');
    }

    /**
     * @test
     */
    public function it_returns_a_response_when_making_a_request_with_a_matching_expectation_defined()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('/test');

        $response = $client->get('/test');

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @test
     */
    public function request_handlers_are_removed_once_they_are_executed()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('/test');

        $response = $client->get('/test');

        $this->expectException(UnhandledRequestException::class);

        $client->get('/test');
    }

    /**
     * @test
     */
    public function request_expectations_can_customize_responses()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('/test')->respondWith(201, 'test');

        $response = $client->get('/test');

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('test', (string) $response->getBody());
    }

    /**
     * @test
     */
    public function request_expectations_can_inspect_requests()
    {
        $ran = false;

        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('/test')->inspectRequest(function (RequestInterface $request) use (&$ran) {
            $this->assertSame('GET', $request->getMethod());
            $this->assertSame('/test', $request->getUri()->getPath());
            $ran = true;
        });

        $client->get('/test');

        $this->assertTrue($ran);
    }

    /**
     * @test
     */
    public function it_can_have_multiple_request_expectations_for_the_same_request()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('/test')->respondWith(200);
        $mockHandler->get('/test')->respondWith(201);

        $this->assertSame(200, $client->get('/test')->getStatusCode());
        $this->assertSame(201, $client->get('/test')->getStatusCode());
    }

    /**
     * @test
     */
    public function it_can_have_multiple_request_expectations_for_the_different_paths()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('/test1')->respondWith(200);
        $mockHandler->get('/test2')->respondWith(201);

        $this->assertSame(200, $client->get('/test1')->getStatusCode());
        $this->assertSame(201, $client->get('/test2')->getStatusCode());
    }

    /**
     * @test
     */
    public function it_can_have_multiple_request_expectations_for_different_methods()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('/test')->respondWith(200);
        $mockHandler->post('/test')->respondWith(201);

        $this->assertSame(200, $client->get('/test')->getStatusCode());
        $this->assertSame(201, $client->post('/test')->getStatusCode());
    }
}
