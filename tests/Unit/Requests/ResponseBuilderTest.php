<?php

namespace Tests\Unit\Requests;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use JSHayes\GuzzleTesting\Requests\ResponseBuilder;

class ResponseBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_an_empty_200_response_by_default()
    {
        $builder = new ResponseBuilder();
        $response = $builder->build();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('', (string) $response->getBody());
        $this->assertSame([], $response->getHeaders());
    }

    /**
     * @test
     */
    public function it_can_set_the_status_of_the_response()
    {
        $builder = new ResponseBuilder();
        $builder->status(400);
        $response = $builder->build();

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('', (string) $response->getBody());
        $this->assertSame([], $response->getHeaders());
    }

    /**
     * @test
     */
    public function it_can_set_the_body_of_the_response_to_string()
    {
        $builder = new ResponseBuilder();
        $builder->body('test');
        $response = $builder->build();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('test', (string) $response->getBody());
        $this->assertSame([], $response->getHeaders());
    }

    /**
     * @test
     */
    public function it_can_set_the_body_of_the_response_to_array()
    {
        $builder = new ResponseBuilder();
        $builder->body(['test' => 'value']);
        $response = $builder->build();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"test":"value"}', (string) $response->getBody());
        $this->assertSame([], $response->getHeaders());
    }

    /**
     * @test
     */
    public function it_can_set_the_body_of_the_response_to_json_serializable_object()
    {
        $builder = new ResponseBuilder();
        $body = new class () implements JsonSerializable {
            public function jsonSerialize(): array
            {
                return ['test' => 'value'];
            }
        };
        $builder->body($body);
        $response = $builder->build();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('{"test":"value"}', (string) $response->getBody());
        $this->assertSame([], $response->getHeaders());
    }

    /**
     * @test
     */
    public function it_can_set_the_headers_of_the_response()
    {
        $builder = new ResponseBuilder();
        $builder->headers(['header' => 'value']);
        $response = $builder->build();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('', (string) $response->getBody());
        $this->assertSame(['header' => ['value']], $response->getHeaders());
    }

    /**
     * @test
     */
    public function it_can_set_status_fluently()
    {
        $builder = new ResponseBuilder();
        $this->assertSame($builder, $builder->status(200));
    }

    /**
     * @test
     */
    public function it_can_set_headers_fluently()
    {
        $builder = new ResponseBuilder();
        $this->assertSame($builder, $builder->headers([]));
    }

    /**
     * @test
     */
    public function it_can_set_body_fluently()
    {
        $builder = new ResponseBuilder();
        $this->assertSame($builder, $builder->body(''));
    }
}
