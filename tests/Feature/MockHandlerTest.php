<?php

namespace Tests\Feature;

use Tests\Doubles\Decorator;
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
        $client = $factory->make($options);
        $client->getConfig('handler')->remove('http_errors');
        return $client;
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

    /**
     * @test
     */
    public function it_matches_paths_with_preceeding_slash()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('test')->respondWith(200);

        $this->assertSame(200, $client->get('/test')->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_the_response_when_the_when_condition_passes()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('/test')->respondWith(200)->when(function () {
            return false;
        });
        $mockHandler->get('/test')->respondWith(404)->when(function () {
            return true;
        });

        $this->assertSame(404, $client->get('/test')->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_the_first_response_that_passes_the_when_condition()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('/test')->respondWith(200)->when(function () {
            return true;
        });
        $mockHandler->get('/test')->respondWith(404)->when(function () {
            return true;
        });

        $this->assertSame(200, $client->get('/test')->getStatusCode());
        $this->assertSame(404, $client->get('/test')->getStatusCode());
    }

    /**
     * @test
     */
    public function it_doesnt_return_a_response_when_none_of_the_when_conditions_pass()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('/test')->respondWith(200)->when(function () {
            return false;
        });
        $mockHandler->get('/test')->respondWith(404)->when(function () {
            return false;
        });

        $this->expectException(UnhandledRequestException::class);
        $client->get('/test');
    }

    /**
     * @test
     */
    public function it_doesnt_return_a_response_when_the_path_does_not_match_but_the_when_condition_passes()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('/test')->respondWith(200)->when(function () {
            return true;
        });

        $this->expectException(UnhandledRequestException::class);
        $client->get('/wat');
    }

    /**
     * @test
     */
    public function the_expects_method_can_be_used_to_make_an_expectation()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->expects('get', '/test')->respondWith(200);

        $this->assertSame(200, $client->get('/test')->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_the_request_after_the_response_is_returned()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $expectation = $mockHandler->expects('get', '/test')->respondWith(200);

        $client->get('/test');

        $request = $expectation->getRequest();
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('/test', $request->getUri()->getPath());
    }

    /**
     * @test
     */
    public function it_returns_null_when_getting_the_request_before_it_is_handled()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $expectation = $mockHandler->expects('get', '/test')->respondWith(200);

        $this->assertNull($expectation->getRequest());
    }

    /**
     * @test
     */
    public function it_can_make_assertions_on_the_request_that_is_returned_from_the_expectation()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $expectation = $mockHandler->expects('get', '/test')->respondWith(200);

        $client->get('/test?query=value', ['headers' => ['header' => ['value']]]);

        $request = $expectation->getRequest();
        $request->assertQueryEquals('query=value');
        $request->assertHasQueryParam('query', 'value');
        $request->assertNotHasQueryParam('missing');
        $request->assertHasHeader('header', 'value');
        $request->assertNotHasHeader('missing');
    }

    /**
     * @test
     */
    public function it_returns_a_response_when_the_expectation_specifies_a_host()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('https://test.dev/test');

        $this->assertSame(200, $client->get('https://test.dev/test')->getStatusCode());
    }

    /**
     * @test
     */
    public function it_matches_expectations_with_just_paths_and_expectations_with_hosts()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->get('https://test.dev/test')->respondWith(200);
        $mockHandler->get('/test')->respondWith(404);

        $this->assertSame(200, $client->get('https://test.dev/test')->getStatusCode());
        $this->assertSame(404, $client->get('https://test.dev/test')->getStatusCode());
    }

    /**
     * @test
     */
    public function it_can_allow_unexpected_api_calls()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->allowUnexpectedCalls();

        $this->assertSame(200, $client->get('https://test.dev/test')->getStatusCode());
        $this->assertSame(200, $client->get('test')->getStatusCode());
    }

    /**
     * @test
     */
    public function it_returns_not_empty_when_expectations_are_not_met_when_unexpected_api_calls_are_allowed()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->allowUnexpectedCalls();
        $mockHandler->expects('GET', '/endpoint');

        $this->assertSame(200, $client->get('https://test.dev/test')->getStatusCode());
        $this->assertFalse($mockHandler->isEmpty());
    }

    /**
     * @test
     */
    public function it_decorates_the_request_handler_when_a_decorator_is_set()
    {
        $client = $this->makeClient($mockHandler = new MockHandler());
        $mockHandler->setDecorator(new Decorator());
        $expectation = $mockHandler->expects('POST', '/test');

        $this->assertSame(200, $client->post('https://test.dev/test', ['json' => ['key' => 'value']])->getStatusCode());

        $this->assertEquals(['key' => 'value'], $expectation->getDecodedRequestBody());
    }
}
