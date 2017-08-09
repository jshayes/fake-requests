<?php

namespace JSHayes\FakeRequests;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RequestHandler
{
    private $callback;
    private $response;

    public function __construct()
    {
        $this->respondWith(function () {});
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
        if (!is_null($this->callback)) {
            call_user_func($this->callback, $request, $options);
        }

        return $this->response;
    }

    /**
     * Add a callback that will have the request and request options passed to
     * it
     *
     * @param callable $callback
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function inspectRequest(callable $callback): RequestHandler
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * Add a response that this handler will response with.
     *
     * @param \Psr\Http\Message\ResponseInterface|callable|int $arg
     *        Can be one of
     *          ResponseInterface
     *          callable, @see respondWithCallback
     *          int, @see responseWithParameters
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function respondWith($arg): RequestHandler
    {
        if ($arg instanceof ResponseInterface) {
            $this->response = $arg;
        } elseif (is_callable($arg)) {
            $this->respondWithCallback($arg);
        } else {
            call_user_func_array([$this, 'respondWithParameters'], func_get_args());
        }

        return $this;
    }

    /**
     * Add a callback that will receive a response builder
     *
     * @param callable $callback
     * @return void
     */
    private function respondWithCallback(callable $callback): void
    {
        $responseBuilder = new ResponseBuilder();
        $callback($responseBuilder);
        $this->response = $responseBuilder->build();
    }

    /**
     * Create a response with the given properties
     *
     * @param int $status
     * @param string|array $body
     * @param array $headers
     * @return void
     */
    private function respondWithParameters(int $status, $body = '', array $headers = []): void
    {
        $this->respondWithCallback(function ($builder) use ($status, $body, $headers) {
            $builder->status($status);
            $builder->body($body);
            $builder->headers($headers);
        });
    }
}
