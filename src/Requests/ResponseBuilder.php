<?php

namespace JSHayes\GuzzleTesting\Requests;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class ResponseBuilder
{
    private $status = 200;
    private $headers = [];
    private $body = '';

    /**
     * Set the status code of this response
     *
     * @param int $status
     * @return \JSHayes\GuzzleTesting\Requests\ResponseBuilder
     */
    public function status(int $status): ResponseBuilder
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Set the headers of this response
     *
     * @param array $headers
     * @return \JSHayes\GuzzleTesting\Requests\ResponseBuilder
     */
    public function headers(array $headers): ResponseBuilder
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Set the body of the response
     *
     * @param string|array|\JsonSerializable $body
     * @return \JSHayes\GuzzleTesting\Requests\ResponseBuilder
     */
    public function body($body): ResponseBuilder
    {
        if (!is_string($body)) {
            $body = json_encode($body);
        }

        $this->body = $body;
        return $this;
    }

    /**
     * Create the response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function build(): ResponseInterface
    {
        return new Response($this->status, $this->headers, $this->body);
    }
}
