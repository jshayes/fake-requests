<?php

namespace JSHayes\FakeRequests;

use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Promise\PromiseInterface;
use JSHayes\FakeRequests\RequestHandler;
use JSHayes\FakeRequests\Exceptions\UnhandledRequestException;

class MockHandler
{
    private $handlers;

    public function __construct()
    {
        $this->handlers = new Collection();
    }

    /**
     * Add a request handler for the given http method and uri
     *
     * @param string $method
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function expects(string $method, string $uri): RequestHandler
    {
        return $this->handlers->push(new RequestHandler($method, $uri))->last();
    }

    /**
     * Add a request handler for the get request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function get(string $uri): RequestHandler
    {
        return $this->expects('GET', $uri);
    }

    /**
     * Add a request handler for the post request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function post(string $uri): RequestHandler
    {
        return $this->expects('POST', $uri);
    }

    /**
     * Add a request handler for the put request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function put(string $uri): RequestHandler
    {
        return $this->expects('PUT', $uri);
    }

    /**
     * Add a request handler for the patch request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function patch(string $uri): RequestHandler
    {
        return $this->expects('PATCH', $uri);
    }

    /**
     * Add a request handler for the delete request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function delete(string $uri): RequestHandler
    {
        return $this->expects('DELETE', $uri);
    }

    /**
     * Add a request handler for the head request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function head(string $uri): RequestHandler
    {
        return $this->expects('HEAD', $uri);
    }

    /**
     * Add a request handler for the options request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function options(string $uri): RequestHandler
    {
        return $this->expects('OPTIONS', $uri);
    }

    /**
     * Determine if there are not more handlers registered
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->handlers->flatten()->isEmpty();
    }

    /**
     * Get the request handlers currently registered with this handler
     *
     * @return \Illuminate\Support\Collection
     */
    public function getHandlers(): Collection
    {
        return $this->handlers;
    }

    /**
     * Find the first request handler that matches the method and path of the
     * given request and execute it
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array $options
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        foreach ($this->handlers as $key => $handler) {
            if ($handler->shouldHandle($request, $options)) {
                $this->handlers->pull($key);
                return Promise\promise_for($handler->handle($request, $options));
            }
        }

        throw new UnhandledRequestException($request);
    }
}
