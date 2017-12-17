<?php

namespace Tests\Unit;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use JSHayes\FakeRequests\RequestHandler;
use JSHayes\FakeRequests\ResponseBuilder;

class RequestHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function handle_returns_a_200_response_by_default()
    {
        $handler = new RequestHandler();
        $response = $handler->handle(new Request('GET', '/test'), []);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('', (string) $response->getBody());
        $this->assertSame([], $response->getHeaders());
    }

    /**
     * @test
     */
    public function you_can_add_a_custom_response()
    {
        $handler = new RequestHandler();
        $response = new Response();
        $handler->respondWith($response);

        $this->assertSame($response, $handler->handle(new Request('GET', '/test'), []));
    }

    /**
     * @test
     */
    public function you_can_customize_response_with_closure()
    {
        $handler = new RequestHandler();
        $handler->respondWith(function (ResponseBuilder $builder) {
            $builder->status(404);
            $builder->body('test body');
            $builder->headers(['header' => 'value']);
        });
        $response = $handler->handle(new Request('GET', '/test'), []);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('test body', (string) $response->getBody());
        $this->assertSame(['header' => ['value']], $response->getHeaders());
    }

    /**
     * @test
     */
    public function you_can_customize_response_with_parameters()
    {
        $handler = new RequestHandler();
        $handler->respondWith(404, 'test body', ['header' => 'value']);
        $response = $handler->handle(new Request('GET', '/test'), []);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('test body', (string) $response->getBody());
        $this->assertSame(['header' => ['value']], $response->getHeaders());
    }

    /**
     * @test
     */
    public function you_can_inspect_the_request_and_options()
    {
        $ran = false;
        $handler = new RequestHandler();
        $handler->inspectRequest(function (RequestInterface $request, array $options) use (&$ran) {
            $this->assertSame('GET', $request->getMethod());
            $this->assertSame('/test', $request->getUri()->getPath());
            $this->assertSame(['option' => 'value'], $options);
            $ran = true;
        });
        $response = $handler->handle(new Request('GET', '/test'), ['option' => 'value']);

        $this->assertTrue($ran);
    }

    /**
     * @test
     */
    public function it_should_handle_requests_by_default()
    {
        $handler = new RequestHandler();
        $this->assertTrue($handler->shouldHandle(new Request('GET', '/test'), []));
    }

    /**
     * @test
     */
    public function it_should_not_handle_requests_when_the_when_condition_fails()
    {
        $handler = new RequestHandler();
        $handler->when(function () {
            return false;
        });
        $this->assertFalse($handler->shouldHandle(new Request('GET', '/test'), []));
    }

    /**
     * @test
     */
    public function it_should_not_handle_requests_when_the_when_condition_passes()
    {
        $handler = new RequestHandler();
        $handler->when(function () {
            return true;
        });
        $this->assertTrue($handler->shouldHandle(new Request('GET', '/test'), []));
    }

    /**
     * @test
     */
    public function you_can_get_the_request_after_one_has_been_handled()
    {
        $handler = new RequestHandler();
        $request = new Request('GET', '/test');

        $handler->handle($request, []);

        $this->assertSame($request, $handler->getRequest());
    }
}
