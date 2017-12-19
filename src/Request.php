<?php

namespace JSHayes\FakeRequests;

use PHPUnit_Framework_Assert;
use Psr\Http\Message\RequestInterface;

class Request extends PHPUnit_Framework_Assert
{
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Delegate method calls to the decorated request
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return call_user_func_array([$this->request, $method], $args);
    }

    /**
     * Assert that the request has the given header. If the value is specified
     * it will ensure that the header has the given value. Otherwise, it will
     * just ensure that there is a header with any value.
     *
     * @param string $header
     * @param string $value
     * @return void
     */
    public function assertHasHeader(string $header, string $value = null): void
    {
        $this->assertArrayHasKey($header, $this->getHeaders(), "The \"$header\" header was not found.");

        if (!is_null($value)) {
            $this->assertContains($value, $this->getHeader($header));
        }
    }

    /**
     * Assert that the request does not have the given header. If the value is
     * specified it will ensure that the header does not have the given value.
     * Otherwise, it will just ensure that there is no header with any value.
     *
     * @param string $header
     * @param string $value
     * @return void
     */
    public function assertNotHasHeader(string $header, string $value = null): void
    {
        if (is_null($value)) {
            $this->assertArrayNotHasKey($header, $this->getHeaders(), "The \"$header\" header was found.");
        } else {
            $this->assertNotContains($value, $this->getHeader($header));
        }
    }

    /**
     * Assert that the request has the given query parameter. If the value is
     * specified it will ensure that the query param with the given key matches
     * the value. Otherwise, it will just ensure that there is a query parameter
     * for the given key.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function assertHasQueryParam(string $key, string $value = null): void
    {
        $query = collect(explode('&', $this->getUri()->getQuery()))->mapWithKeys(function ($value) {
            list($key, $value) = array_pad(explode('=', $value), 2, null);
            return [$key => $value];
        });

        $this->assertArrayHasKey($key, $query, "The \"$key\" query param was not found.");

        if (!is_null($value)) {
            $this->assertEquals($value, $query->get($key));
        }
    }

    /**
     * Assert that the request does not have the given query parameter. If the
     * value is specified it will ensure that the query param with the given key
     * does not match the value. Otherwise, it will just ensure that there is no
     * query parameter for the given key.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function assertNotHasQueryParam(string $key, string $value = null): void
    {
        $query = collect(explode('&', $this->getUri()->getQuery()))->mapWithKeys(function ($value) {
            list($key, $value) = array_pad(explode('=', $value), 2, null);
            return [$key => $value];
        });

        if (is_null($value)) {
            $this->assertArrayNotHasKey($key, $query, "The \"$key\" query param was not found.");
        } else {
            $this->assertNotEquals($value, $query->get($key));
        }
    }

    /**
     * Assert that the query string matched the given query string
     *
     * @param string $query
     * @return void
     */
    public function assertQueryEquals(string $query): void
    {
        $this->assertEquals($query, $this->getUri()->getQuery());
    }

    /**
     * Assert that the request body matches the given string
     *
     * @param string $body
     * @return void
     */
    public function assertBodyEquals(string $body): void
    {
        $this->assertEquals($body, (string) $this->getBody());
    }
}
