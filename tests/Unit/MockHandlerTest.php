<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use JSHayes\FakeRequests\MockHandler;
use JSHayes\FakeRequests\Requests\Handler;

class MockHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function get_returns_a_request_handler()
    {
        $this->assertInstanceOf(Handler::class, (new MockHandler())->get('/test'));
    }

    /**
     * @test
     */
    public function post_returns_a_request_handler()
    {
        $this->assertInstanceOf(Handler::class, (new MockHandler())->post('/test'));
    }

    /**
     * @test
     */
    public function put_returns_a_request_handler()
    {
        $this->assertInstanceOf(Handler::class, (new MockHandler())->put('/test'));
    }

    /**
     * @test
     */
    public function patch_returns_a_request_handler()
    {
        $this->assertInstanceOf(Handler::class, (new MockHandler())->patch('/test'));
    }

    /**
     * @test
     */
    public function delete_returns_a_request_handler()
    {
        $this->assertInstanceOf(Handler::class, (new MockHandler())->delete('/test'));
    }

    /**
     * @test
     */
    public function head_returns_a_request_handler()
    {
        $this->assertInstanceOf(Handler::class, (new MockHandler())->head('/test'));
    }

    /**
     * @test
     */
    public function options_returns_a_request_handler()
    {
        $this->assertInstanceOf(Handler::class, (new MockHandler())->options('/test'));
    }
}
