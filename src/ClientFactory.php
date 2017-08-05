<?php

namespace JSHayes\GuzzleTesting;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

class ClientFactory
{
    private $handler;

    /**
     * Create a new client with the given options. If the Client uses the
     * HandlerStack it will replace the inner handler, keeping the middleware
     * intact.
     *
     * @param array $options
     * @return \GuzzleHttp\Client
     */
    public function make(array $options = []): Client
    {
        if ($this->handler) {
            $handler = $options['handler'] ?? HandlerStack::create();
            if ($handler instanceof HandlerStack) {
                $handler->setHandler($this->handler);
            } else {
                $handler = $this->handler;
            }
            $options['handler'] = $handler;
        }

        return new Client($options);
    }

    /**
     * Set the request handler to use for the client.
     *
     * @param callable $handler
     * @return void
     */
    public function setHandler(callable $handler): void
    {
        $this->handler = $handler;
    }
}
