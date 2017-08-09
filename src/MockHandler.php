<?php

namespace JSHayes\FakeRequests;

use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Collection;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Promise\PromiseInterface;
use JSHayes\FakeRequests\Requests\Handler;
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
     * @return \JSHayes\FakeRequests\Requests\Handler
     */
    private function addRequestHandler(string $method, string $path): Handler
    {
        $path = ltrim($path, '/');
        $handler = new Handler();

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
     * @return \JSHayes\FakeRequests\Requests\Handler
     */
    public function get(string $path): Handler
    {
        return $this->addRequestHandler('GET', $path);
    }

    /**
     * Add a request handler for the post request to the given path
     *
     * @param string $path
     * @return \JSHayes\FakeRequests\Requests\Handler
     */
    public function post(string $path): Handler
    {
        return $this->addRequestHandler('POST', $path);
    }

    /**
     * Add a request handler for the put request to the given path
     *
     * @param string $path
     * @return \JSHayes\FakeRequests\Requests\Handler
     */
    public function put(string $path): Handler
    {
        return $this->addRequestHandler('PUT', $path);
    }

    /**
     * Add a request handler for the patch request to the given path
     *
     * @param string $path
     * @return \JSHayes\FakeRequests\Requests\Handler
     */
    public function patch(string $path): Handler
    {
        return $this->addRequestHandler('PATCH', $path);
    }

    /**
     * Add a request handler for the delete request to the given path
     *
     * @param string $path
     * @return \JSHayes\FakeRequests\Requests\Handler
     */
    public function delete(string $path): Handler
    {
        return $this->addRequestHandler('DELETE', $path);
    }

    /**
     * Add a request handler for the head request to the given path
     *
     * @param string $path
     * @return \JSHayes\FakeRequests\Requests\Handler
     */
    public function head(string $path): Handler
    {
        return $this->addRequestHandler('HEAD', $path);
    }

    /**
     * Add a request handler for the options request to the given path
     *
     * @param string $path
     * @return \JSHayes\FakeRequests\Requests\Handler
     */
    public function options(string $path): Handler
    {
        return $this->addRequestHandler('OPTIONS', $path);
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

        $handler = $pathHandlers->shift();
        return Promise\promise_for($handler->handle($request, $options));
    }
}
