<?php

namespace JSHayes\FakeRequests\Contracts;

use GuzzleHttp\Psr7\Uri;
use JSHayes\FakeRequests\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RequestHandler
{
    /**
     * Get the Uri for this request handler
     *
     * @return \GuzzleHttp\Psr7\Uri
     */
    public function getUri(): Uri;

    /**
     * Get the request method for this request handler
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * Determine if this request should be handled
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param arrar $options
     * @return bool
     */
    public function shouldHandle(RequestInterface $request, array $options): bool;

    /**
     * Pass the request to the request inspector if specified, then return the
     * response.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(RequestInterface $request, array $options): ResponseInterface;

    /**
     * Add a callback that will have the request and request options passed to
     * it
     *
     * @param callable $callback
     * @return \JSHayes\FakeRequests\Contracts\RequestHandler
     */
    public function inspectRequest(callable $callback): RequestHandler;

    /**
     * Set the callback that determines when this request should be handled
     *
     * @param callable $callback
     * @return \JSHayes\FakeRequests\Contracts\RequestHandler
     */
    public function when(callable $callback): RequestHandler;

    /**
     * Add a response that this handler will response with.
     *
     * @param \Psr\Http\Message\ResponseInterface|callable|int $arg
     * @return \JSHayes\FakeRequests\Contracts\RequestHandler
     */
    public function respondWith($arg): RequestHandler;

    /**
     * Return the request that this handler handled
     *
     * @return \JSHayes\FakeRequests\Request|null
     */
    public function getRequest(): ?Request;
}
