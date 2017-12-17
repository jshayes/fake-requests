<?php

namespace JSHayes\FakeRequests;

use GuzzleHttp\Promise;
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
     * Add a request handler for the given http method and path
     *
     * @param string $method
     * @param string $path
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function expects(string $method, string $path): RequestHandler
    {
        $method = strtoupper($method);
        $path = ltrim($path, '/');
        $handler = new RequestHandler();

        $methodHandlers = $this->handlers->get($method, new Collection());
        $pathHandlers = $methodHandlers->get($path, new Collection());
        $pathHandlers->push($handler);
        $methodHandlers->put($path, $pathHandlers);
        $this->handlers->put($method, $methodHandlers);

        return $handler;
    }

    /**
     * Add a request handler for the get request to the given path
     *
     * @param string $path
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function get(string $path): RequestHandler
    {
        return $this->expects('GET', $path);
    }

    /**
     * Add a request handler for the post request to the given path
     *
     * @param string $path
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function post(string $path): RequestHandler
    {
        return $this->expects('POST', $path);
    }

    /**
     * Add a request handler for the put request to the given path
     *
     * @param string $path
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function put(string $path): RequestHandler
    {
        return $this->expects('PUT', $path);
    }

    /**
     * Add a request handler for the patch request to the given path
     *
     * @param string $path
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function patch(string $path): RequestHandler
    {
        return $this->expects('PATCH', $path);
    }

    /**
     * Add a request handler for the delete request to the given path
     *
     * @param string $path
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function delete(string $path): RequestHandler
    {
        return $this->expects('DELETE', $path);
    }

    /**
     * Add a request handler for the head request to the given path
     *
     * @param string $path
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function head(string $path): RequestHandler
    {
        return $this->expects('HEAD', $path);
    }

    /**
     * Add a request handler for the options request to the given path
     *
     * @param string $path
     * @return \JSHayes\FakeRequests\RequestHandler
     */
    public function options(string $path): RequestHandler
    {
        return $this->expects('OPTIONS', $path);
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
     * Find the first request handler that matches the method and path of the
     * given request and execute it
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array $options
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options): PromiseInterface
    {
        $method = strtoupper($request->getMethod());
        $path = ltrim($request->getUri()->getPath(), '/');

        $methodHandlers = $this->handlers->get($method, new Collection());
        $pathHandlers = $methodHandlers->get($path, new Collection());

        if ($pathHandlers->isEmpty()) {
            throw new UnhandledRequestException($method, $path);
        }

        foreach ($pathHandlers as $key => $handler) {
            if ($handler->shouldHandle($request, $options)) {
                $pathHandlers->pull($key);
                return Promise\promise_for($handler->handle($request, $options));
            }
        }

        throw new UnhandledRequestException($method, $path);
    }
}
