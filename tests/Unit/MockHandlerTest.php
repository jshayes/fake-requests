<?php

namespace Tests\Unit;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use JSHayes\FakeRequests\MockHandler;
use JSHayes\FakeRequests\RequestHandler;

class MockHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function get_returns_a_request_handler()
    {
        $this->assertInstanceOf(RequestHandler::class, (new MockHandler())->get('/test'));
    }

    /**
     * @test
     */
    public function post_returns_a_request_handler()
    {
        $this->assertInstanceOf(RequestHandler::class, (new MockHandler())->post('/test'));
    }

    /**
     * @test
     */
    public function put_returns_a_request_handler()
    {
        $this->assertInstanceOf(RequestHandler::class, (new MockHandler())->put('/test'));
    }

    /**
     * @test
     */
    public function patch_returns_a_request_handler()
    {
        $this->assertInstanceOf(RequestHandler::class, (new MockHandler())->patch('/test'));
    }

    /**
     * @test
     */
    public function delete_returns_a_request_handler()
    {
        $this->assertInstanceOf(RequestHandler::class, (new MockHandler())->delete('/test'));
    }

    /**
     * @test
     */
    public function head_returns_a_request_handler()
    {
        $this->assertInstanceOf(RequestHandler::class, (new MockHandler())->head('/test'));
    }

    /**
     * @test
     */
    public function options_returns_a_request_handler()
    {
        $this->assertInstanceOf(RequestHandler::class, (new MockHandler())->options('/test'));
    }

    /**
     * @test
     */
    public function is_empty_returns_true_when_no_expectations_are_set()
    {
        $this->assertTrue((new MockHandler())->isEmpty());
    }

    /**
     * @test
     */
    public function is_empty_returns_false_when_expectations_are_set()
    {
        $handler = new MockHandler();
        $handler->get('test');
        $this->assertFalse($handler->isEmpty());
    }

    /**
     * @test
     */
    public function is_empty_returns_false_when_not_all_expectations_are_consumed()
    {
        $handler = new MockHandler();
        $handler->get('test');
        $handler->get('test');

        $request = new Request('GET', 'http://test.dev/test');
        $handler($request, []);

        $this->assertFalse($handler->isEmpty());
    }

    /**
     * @test
     */
    public function is_empty_returns_true_when_expectations_are_consumed()
    {
        $handler = new MockHandler();
        $handler->get('test');

        $request = new Request('GET', 'http://test.dev/test');
        $handler($request, []);

        $this->assertTrue($handler->isEmpty());
    }
}
