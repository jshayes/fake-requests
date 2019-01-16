<?php

namespace JSHayes\FakeRequests;

use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Promise\PromiseInterface;
use JSHayes\FakeRequests\Exceptions\UnhandledRequestException;
use JSHayes\FakeRequests\Contracts\RequestHandler as RequestHandlerContract;

class MockHandler
{
    private $decorator;
    private $handlers;
    private $allowsUnexpected = false;

    public function __construct()
    {
        $this->handlers = new Collection();
    }

    /**
     * Decorate the given request handler if a decorator has be set
     *
     * @param \JSHayes\FakeRequests\Contracts\RequestHandler $handler
     * @return \JSHayes\FakeRequests\Contracts\RequestHandler
     */
    private function decorate(RequestHandlerContract $handler): RequestHandlerContract
    {
        return $this->decorator ? $this->decorator->decorate($handler) : $handler;
    }

    /**
     * Add a request handler for the given http method and uri
     *
     * @param string $method
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function expects(string $method, string $uri): RequestHandlerContract
    {
        return $this->handlers->push($this->decorate(new RequestHandler($method, $uri)))->last();
    }

    /**
     * Add a request handler for the get request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function get(string $uri): RequestHandlerContract
    {
        return $this->expects('GET', $uri);
    }

    /**
     * Add a request handler for the post request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function post(string $uri): RequestHandlerContract
    {
        return $this->expects('POST', $uri);
    }

    /**
     * Add a request handler for the put request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function put(string $uri): RequestHandlerContract
    {
        return $this->expects('PUT', $uri);
    }

    /**
     * Add a request handler for the patch request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function patch(string $uri): RequestHandlerContract
    {
        return $this->expects('PATCH', $uri);
    }

    /**
     * Add a request handler for the delete request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function delete(string $uri): RequestHandlerContract
    {
        return $this->expects('DELETE', $uri);
    }

    /**
     * Add a request handler for the head request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function head(string $uri): RequestHandlerContract
    {
        return $this->expects('HEAD', $uri);
    }

    /**
     * Add a request handler for the options request to the given uri
     *
     * @param string $uri
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function options(string $uri): RequestHandlerContract
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
     * Allows unexpected calls to this handler. When this is set, any call that
     * does not have an expectation define will return a generic response.
     *
     * @return \JSHayes\FakeRequests\MockHandler
     */
    public function allowUnexpectedCalls(): MockHandler
    {
        $this->allowsUnexpected = true;
        return $this;
    }

    /**
     * Set a decorator that will decorate the request handlers
     *
     * @param \JSHayes\FakeRequests\Decorator $decorator
     * @return \JSHayes\FakeRequests\MockHandler
     */
    public function setDecorator(Decorator $decorator): MockHandler
    {
        $this->decorator = $decorator;
        return $this;
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

        if ($this->allowsUnexpected) {
            return Promise\promise_for(new Response());
        }

        throw new UnhandledRequestException($request);
    }
}
