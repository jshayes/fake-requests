<?php

namespace Tests\Unit;

use GuzzleHttp\Psr7\Uri;
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
        $handler = new RequestHandler('GET', '/test');
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
        $handler = new RequestHandler('GET', '/test');
        $response = new Response();
        $handler->respondWith($response);

        $this->assertSame($response, $handler->handle(new Request('GET', '/test'), []));
    }

    /**
     * @test
     */
    public function you_can_customize_response_with_closure()
    {
        $handler = new RequestHandler('GET', '/test');
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
        $handler = new RequestHandler('GET', '/test');
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
        $handler = new RequestHandler('GET', '/test');
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
        $handler = new RequestHandler('GET', '/test');
        $this->assertTrue($handler->shouldHandle(new Request('GET', '/test'), []));
    }

    /**
     * @test
     */
    public function it_should_not_handle_requests_when_the_when_condition_fails()
    {
        $handler = new RequestHandler('GET', '/test');
        $handler->when(function () {
            return false;
        });
        $this->assertFalse($handler->shouldHandle(new Request('GET', '/test'), []));
    }

    /**
     * @test
     */
    public function it_should_handle_requests_when_the_when_condition_passes()
    {
        $handler = new RequestHandler('GET', '/test');
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
        $handler = new RequestHandler('GET', '/test');
        $request = new Request('GET', '/test');

        $handler->handle($request, []);

        $this->assertSame($request, $handler->getRequest());
    }

    /**
     * @test
     */
    public function it_should_not_handle_requests_when_the_method_does_not_match()
    {
        $handler = new RequestHandler('GET', '/test');
        $handler->when(function () {
            return true;
        });
        $this->assertFalse($handler->shouldHandle(new Request('POST', '/test'), []));
    }

    /**
     * @test
     */
    public function it_should_not_handle_requests_when_the_path_does_not_match()
    {
        $handler = new RequestHandler('GET', '/test');
        $handler->when(function () {
            return true;
        });
        $this->assertFalse($handler->shouldHandle(new Request('GET', '/wat'), []));
    }

    /**
     * @test
     */
    public function it_should_handle_the_request_when_it_matches_the_host_and_path()
    {
        $handler = new RequestHandler('GET', 'http://test.dev/test');
        $handler->when(function () {
            return true;
        });
        $this->assertTrue($handler->shouldHandle(new Request('GET', 'http://test.dev/test'), []));
    }

    /**
     * @test
     */
    public function it_should_not_handle_the_request_when_it_does_not_match_the_host()
    {
        $handler = new RequestHandler('GET', 'http://test.dev/test');
        $handler->when(function () {
            return true;
        });
        $this->assertFalse($handler->shouldHandle(new Request('GET', 'http://incorrect.dev/test'), []));
    }

    /**
     * @test
     */
    public function it_should_not_handle_the_request_when_it_does_not_match_the_scheme()
    {
        $handler = new RequestHandler('GET', 'http://test.dev/test');
        $handler->when(function () {
            return true;
        });
        $this->assertFalse($handler->shouldHandle(new Request('GET', 'https://test.dev/test'), []));
    }

    /**
     * @test
     */
    public function it_should_not_handle_the_request_when_it_does_not_match_the_path()
    {
        $handler = new RequestHandler('GET', 'http://test.dev/test');
        $handler->when(function () {
            return true;
        });
        $this->assertFalse($handler->shouldHandle(new Request('GET', 'http://test.dev/incorrect'), []));
    }

    /**
     * @test
     */
    public function it_should_handle_the_request_when_the_path_does_not_have_a_preceeding_slack()
    {
        $handler = new RequestHandler('GET', 'test');
        $handler->when(function () {
            return true;
        });
        $this->assertTrue($handler->shouldHandle(new Request('GET', '/test'), []));
    }
}
