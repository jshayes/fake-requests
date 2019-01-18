<?php

namespace Tests\Doubles;

use JSHayes\FakeRequests\Request;

class ExtendedRequest extends Request
{
    /**
     * This is an example method in an extended request. These extended requests
     * can be used to add assertion helpers to make testing request flows easier
     *
     * @return void
     */
    public function extendedMethod(): void
    {
        $this->assertTrue(true);
    }
}
