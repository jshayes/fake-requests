<?php

namespace Tests;

use Closure;

trait CanSnoopObjects
{
    /**
     * Bind the given callback to the given object, then execute it. This allows
     * you to access private state on the object from within the closure
     *
     * @param Object $obj
     * @param Closure $callback
     * @return mixed
     */
    private function snoop($obj, Closure $callback)
    {
        return $callback->bindTo($obj, get_class($obj))();
    }
}
