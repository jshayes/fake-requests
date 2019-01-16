<?php

namespace Tests\Doubles;

use JSHayes\FakeRequests\Decorator as BaseDecorator;

class Decorator extends BaseDecorator
{
    /**
     * An example decorated method to get the decoded request body
     *
     * @return array
     */
    public function getDecodedRequestBody(): array
    {
        return json_decode($this->getRequest()->getBody(), true);
    }
}
