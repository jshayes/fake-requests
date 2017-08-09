![](https://travis-ci.org/jshayes/fake-requests.svg?branch=master)

# fake-requests
A simple package to make testing with guzzle easier

## Basic usage
You can register expected calls to the `MockHandler`. The mock handler has methods for each http request type.

```PHP
use JSHayes\FakeRequests\MockHandler;
use JSHayes\FakeRequests\ClientFactory;

public function test() 
{
    $factory = new ClientFactory();
    $factory->setHandler($mockHandler = new MockHandler());
    
    $mockHandler->get('/get-request');
    $mockHandler->post('/post-request');
    
    $factory->make()->get('/get-request');
    $factory->make()->post('/post-request');
}
```

This simple example creates two expectations. The first is a `GET` request with the URL path of `/get-request`. The second is a `POST` request with a URL path of `/post-request`. The client is then resolved out of the factory, and `GET` and `POST` requests are made for each expectation.

The `ClientFactory` can be used to resolve guzzle client instances. You can bind a handler to the factory so that when it resolves the guzzle client it will swap out the default handler with the one that you have specified. It will also keep the registered middleware intact in the case that you are using the `HandlerStack`.

Once an expectation is met, it is removed from the handler. So if you make the same request twice you have to add two separate expectations.

## Inspecting the request
If you need to make assertions on the request that created, or the options that are provided, you can use the `inspectRequest` method. This method receives an instance of `\Psr\Http\Message\RequestInterface` as the first parameter.
```PHP
$mockHandler->get('/test')->inspectRequest(function (RequestInterface $request, array $options) {
    // Make assertions on the request or options here
});
```

## Customizing the response
There are a few ways to create a custom response for each expectation. When you create a custom response, that response is what will be returned to the guzzle client when the request expectation is met. The three ways to customize the response are as follows.

The first method is by passing in the parameters for the request. The first parameter is the status code. The second is the body of the response. The third is the array of headers to add to the response.
```PHP
$mockHandler->get('/test')->respondWith(200, 'body', ['header' => 'value]);
```

The second method is by creating a request that implements `\Psr\Http\Message\ResponseInterface`.
```PHP
$mockHandler->get('/test')->respondWith(new Response(200, ['header' => 'value'], 'body'));
```

The third method is by passing a callback to `respondWith`. This callback will receive an instance of `JSHayes\FakeRequests\ResponseBuilder`
```PHP
$mockHandler->get('/test')->respondWith(function (ResponseBuilder $builder) {
    $builder->status(200);
    $builder->body('body');
    $builder->headers(['header' => 'values']);
});
```

## Testing with Laravel
This package also comes with a trait to make testing with Laravel a bit easier.

```PHP
use JSHayes\FakeRequests\Traits\Laravel\FakeRequests;

class SomeTest extends TestCase
{
    use FakeRequests;
    
    /**
     * @test
     */
    public function some_test()
    {
        $handler = $this->fakeRequests();
        // Add expectations to the handler
    }
}
```

In this example, the `fakeRequests` method created the `MockHandler` for you. It will also bind it to the `ClientFactory` and bind the `ClientFactory` instance to the IOC. If you resolve the `ClientFactory` out of the IOC in you code, this trait will allow you to easily use the `MockHandler` in all of you guzzle client instances.
