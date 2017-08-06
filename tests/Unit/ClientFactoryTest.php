<?php

namespace Tests\Unit;

use Tests\CanSnoopObjects;
use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;
use JSHayes\FakeRequests\ClientFactory;

class ClientFactoryTest extends TestCase
{
    use CanSnoopObjects;

    /**
     * @test
     */
    public function creating_a_client_with_no_handler_uses_the_handler_stack()
    {
        $factory = new ClientFactory();
        $client = $factory->make();

        $this->assertInstanceOf(HandlerStack::class, $client->getConfig('handler'));
    }

    /**
     * @test
     */
    public function creating_a_client_with_a_handler_uses_that_handler()
    {
        $factory = new ClientFactory();
        $handler = function () {};
        $client = $factory->make(['handler' => $handler]);

        $this->assertSame($handler, $client->getConfig('handler'));
    }

    /**
     * @test
     */
    public function creating_a_client_with_no_handler_and_a_factory_handler_override_uses_the_handler_stack_with_the_factory_handler()
    {
        $factory = new ClientFactory();
        $handler = function () {};
        $factory->setHandler($handler);
        $client = $factory->make();

        $innerHandler = $this->snoop($client->getConfig('handler'), function () {
            return $this->handler;
        });
        $this->assertInstanceOf(HandlerStack::class, $client->getConfig('handler'));
        $this->assertSame($handler, $innerHandler);
    }

    /**
     * @test
     */
    public function creating_a_client_with_a_handler_stack_and_a_factory_handler_override_uses_the_handler_stack_with_the_factory_handler()
    {
        $factory = new ClientFactory();
        $handlerStack = HandlerStack::create();
        $handler = function () {};
        $factory->setHandler($handler);
        $client = $factory->make(['handler' => $handlerStack]);

        $innerHandler = $this->snoop($client->getConfig('handler'), function () {
            return $this->handler;
        });
        $this->assertSame($handlerStack, $client->getConfig('handler'));
        $this->assertSame($handler, $innerHandler);
    }

    /**
     * @test
     */
    public function creating_a_client_with_a_handler_and_a_factory_handler_override_uses_the_factory_handler()
    {
        $factory = new ClientFactory();
        $handler = function () {};
        $factory->setHandler($handler);
        $client = $factory->make(['handler' => function () {}]);

        $this->assertSame($handler, $client->getConfig('handler'));
    }
}
