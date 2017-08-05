<?php

namespace Tests\Unit\Requests;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use JSHayes\GuzzleTesting\Requests\Handler;
use JSHayes\GuzzleTesting\Requests\ResponseBuilder;

class HandlerTest extends TestCase
{
    /**
     * @test
     */
    public function handle_returns_a_200_response_by_default()
    {
        $handler = new Handler();
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
        $handler = new Handler();
        $response = new Response();
        $handler->respondWith($response);

        $this->assertSame($response, $handler->handle(new Request('GET', '/test'), []));
    }

    /**
     * @test
     */
    public function you_can_customize_response_with_closure()
    {
        $handler = new Handler();
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
        $handler = new Handler();
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
        $handler = new Handler();
        $handler->inspectRequest(function (RequestInterface $request, array $options) use (&$ran) {
            $this->assertSame('GET', $request->getMethod());
            $this->assertSame('/test', $request->getUri()->getPath());
            $this->assertSame(['option' => 'value'], $options);
            $ran = true;
        });
        $response = $handler->handle(new Request('GET', '/test'), ['option' => 'value']);

        $this->assertTrue($ran);
    }
}
