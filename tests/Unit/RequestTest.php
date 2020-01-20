<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use JSHayes\FakeRequests\Request;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use PHPUnit\Framework\ExpectationFailedException;

class RequestTest extends TestCase
{
    /**
     * @test
     */
    public function assert_has_header_fails_when_the_header_does_not_exist()
    {
        $request = new Request(new GuzzleRequest('get', '/test'));
        $this->expectException(ExpectationFailedException::class);
        $request->assertHasHeader('header');
    }

    /**
     * @test
     */
    public function assert_has_header_succeeds_when_the_header_exists()
    {
        $request = new Request(new GuzzleRequest('get', '/test', ['header' => ['value']]));
        $request->assertHasHeader('header');
    }

    /**
     * @test
     */
    public function assert_has_header_fails_when_the_header_with_the_specified_value_does_not_exist()
    {
        $request = new Request(new GuzzleRequest('get', '/test', ['header' => ['notValue']]));
        $this->expectException(ExpectationFailedException::class);
        $request->assertHasHeader('header', 'value');
    }

    /**
     * @test
     */
    public function assert_has_header_succeeds_when_the_header_with_the_specified_value_exists()
    {
        $request = new Request(new GuzzleRequest('get', '/test', ['header' => ['value']]));
        $request->assertHasHeader('header', 'value');
    }

    /**
     * @test
     */
    public function assert_not_has_header_fails_when_the_header_exists()
    {
        $request = new Request(new GuzzleRequest('get', '/test', ['header' => ['value']]));
        $this->expectException(ExpectationFailedException::class);
        $request->assertNotHasHeader('header');
    }

    /**
     * @test
     */
    public function assert_not_has_header_succeeds_when_the_header_does_not_exist()
    {
        $request = new Request(new GuzzleRequest('get', '/test'));
        $request->assertNotHasHeader('header');
    }

    /**
     * @test
     */
    public function assert_not_has_header_fails_when_the_header_with_the_specified_value_exists()
    {
        $request = new Request(new GuzzleRequest('get', '/test', ['header' => ['value']]));
        $this->expectException(ExpectationFailedException::class);
        $request->assertNotHasHeader('header', 'value');
    }

    /**
     * @test
     */
    public function assert_not_has_header_succeeds_when_the_header_with_the_specified_value_does_not_exist()
    {
        $request = new Request(new GuzzleRequest('get', '/test', ['header' => ['notValue']]));
        $request->assertNotHasHeader('header', 'value');
    }

    /**
     * @test
     */
    public function assert_not_has_header_succeeds_when_the_header_with_the_specified_key_does_not_exist()
    {
        $request = new Request(new GuzzleRequest('get', '/test'));
        $request->assertNotHasHeader('header', 'value');
    }

    /**
     * @test
     */
    public function assert_has_query_param_fails_when_the_query_param_does_not_exist()
    {
        $request = new Request(new GuzzleRequest('get', '/test'));
        $this->expectException(ExpectationFailedException::class);
        $request->assertHasQueryParam('query');
    }

    /**
     * @test
     */
    public function assert_has_query_param_succeeds_when_the_query_param_exists()
    {
        $request = new Request(new GuzzleRequest('get', '/test?query=value'));
        $request->assertHasQueryParam('query');
    }

    /**
     * @test
     */
    public function assert_has_query_param_fails_when_the_query_param_does_not_have_the_specified_value()
    {
        $request = new Request(new GuzzleRequest('get', '/test?query=value'));
        $this->expectException(ExpectationFailedException::class);
        $request->assertHasQueryParam('query', 'incorrect');
    }

    /**
     * @test
     */
    public function assert_has_query_param_succeeds_when_the_query_param_has_the_specified_value()
    {
        $request = new Request(new GuzzleRequest('get', '/test?query=value'));
        $request->assertHasQueryParam('query', 'value');
    }

    /**
     * @test
     */
    public function assert_not_has_query_param_fails_when_the_query_param_exists()
    {
        $request = new Request(new GuzzleRequest('get', '/test?query=value'));
        $this->expectException(ExpectationFailedException::class);
        $request->assertNotHasQueryParam('query');
    }

    /**
     * @test
     */
    public function assert_not_has_query_param_succeeds_when_the_query_param_does_not_exist()
    {
        $request = new Request(new GuzzleRequest('get', '/test'));
        $request->assertNotHasQueryParam('query');
    }

    /**
     * @test
     */
    public function assert_not_has_query_param_fails_when_the_query_param_has_the_specified_value()
    {
        $request = new Request(new GuzzleRequest('get', '/test?query=value'));
        $this->expectException(ExpectationFailedException::class);
        $request->assertNotHasQueryParam('query', 'value');
    }

    /**
     * @test
     */
    public function assert_not_has_query_param_succeeds_when_the_query_param_does_not_have_the_specified_value()
    {
        $request = new Request(new GuzzleRequest('get', '/test?query=value'));
        $request->assertNotHasQueryParam('query', 'incorrect');
    }

    /**
     * @test
     */
    public function assert_not_has_query_param_succeeds_when_the_query_param_does_not_have_the_specified_key()
    {
        $request = new Request(new GuzzleRequest('get', '/test?query=value'));
        $request->assertNotHasQueryParam('incorrect', 'value');
    }

    /**
     * @test
     */
    public function assert_query_equals_fails_when_the_query_string_does_not_match()
    {
        $request = new Request(new GuzzleRequest('get', '/test?query=value'));
        $this->expectException(ExpectationFailedException::class);
        $request->assertQueryEquals('query=incorrect');
    }

    /**
     * @test
     */
    public function assert_query_equals_succeeds_when_the_query_string_matches()
    {
        $request = new Request(new GuzzleRequest('get', '/test?query=value'));
        $request->assertQueryEquals('query=value');
    }

    /**
     * @test
     */
    public function assert_body_equals_fails_when_the_body_string_does_not_match()
    {
        $request = new Request(new GuzzleRequest('get', '/test', [], 'das body'));
        $this->expectException(ExpectationFailedException::class);
        $request->assertQueryEquals('body');
    }

    /**
     * @test
     */
    public function assert_body_equals_succeeds_when_the_body_string_matches()
    {
        $request = new Request(new GuzzleRequest('get', '/test', [], 'das body'));
        $request->assertBodyEquals('das body');
    }
}
