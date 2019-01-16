<?php

namespace JSHayes\FakeRequests;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use JSHayes\FakeRequests\Contracts\RequestHandler as RequestHandlerContract;

abstract class Decorator implements RequestHandlerContract
{
    private $handler;

    /**
     * Decorate the given request handler
     *
     * @param \JSHayes\FakeRequests\Contracts\RequestHandler $handler
     * @return \JSHayes\FakeRequests\Contracts\RequestHandler
     */
    public function decorate(RequestHandlerContract $handler): RequestHandlerContract
    {
        $decorator = new static();
        $decorator->handler = $handler;
        return $decorator;
    }

    /**
     * Get the Uri for this request handler
     *
     * @return \GuzzleHttp\Psr7\Uri
     */
    public function getUri(): Uri
    {
        return $this->handler->getUri();
    }

    /**
     * Get the request method for this request handler
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->handler->getMethod();
    }

    /**
     * Determine if this request should be handled
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param arrar $options
     * @return bool
     */
    public function shouldHandle(RequestInterface $request, array $options): bool
    {
        return $this->handler->shouldHandle($request, $options);
    }

    /**
     * Pass the request to the request inspector if specified, then return the
     * response.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(RequestInterface $request, array $options): ResponseInterface
    {
        return $this->handler->handle($request, $options);
    }

    /**
     * Add a callback that will have the request and request options passed to
     * it
     *
     * @param callable $callback
     * @return \JSHayes\FakeRequests\Contracts\RequestHandler
     */
    public function inspectRequest(callable $callback): RequestHandlerContract
    {
        $this->handler->inspectRequest($callback);
        return $this;
    }

    /**
     * Set the callback that determines when this request should be handled
     *
     * @param callable $callback
     * @return \JSHayes\FakeRequests\Contracts\RequestHandler
     */
    public function when(callable $callback): RequestHandlerContract
    {
        $this->handler->when($callback);
        return $this;
    }

    /**
     * Add a response that this handler will response with.
     *
     * @param \Psr\Http\Message\ResponseInterface|callable|int $arg
     * @return \JSHayes\FakeRequests\Contracts\RequestHandler
     */
    public function respondWith($arg): RequestHandlerContract
    {
        $this->handler->respondWith(...func_get_args());
        return $this;
    }

    /**
     * Return the request that this handler handled
     *
     * @return \JSHayes\FakeRequests\Request|null
     */
    public function getRequest(): ?Request
    {
        return $this->handler->getRequest();
    }
}
