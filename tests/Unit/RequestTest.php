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
        $request = new Request(new GuzzleRequest('get', '/test', ['header' => []]));
        $request->assertHasHeader('header');
    }

    /**
     * @test
     */
    public function assert_has_header_fails_when_the_header_with_the_specified_value_does_not_exist()
    {
        $request = new Request(new GuzzleRequest('get', '/test', ['header' => []]));
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
        $request = new Request(new GuzzleRequest('get', '/test', ['header' => []]));
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
        $request = new Request(new GuzzleRequest('get', '/test', ['header' => []]));
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

    /**
     * @test
     */
    public function get_json_body_returns_the_json_decoded_body()
    {
        $request = new Request(new GuzzleRequest('get', '/test', [], '{"key":"value"}'));
        $this->assertEquals(['key' => 'value'], $request->getJsonBody());
    }

    /**
     * @test
     */
    public function assert_json_body_equals_succeeds_when_the_given_array_equals_the_body()
    {
        $request = new Request(new GuzzleRequest('get', '/test', [], '{"key":"value"}'));
        $request->assertJsonBodyEquals(['key' => 'value']);
    }

    /**
     * @test
     */
    public function assert_json_body_equals_fails_when_the_given_array_does_not_equal_the_body()
    {
        $request = new Request(new GuzzleRequest('get', '/test', [], '{"key":"value"}'));
        $this->expectException(ExpectationFailedException::class);
        $request->assertJsonBodyEquals(['key' => 'invalid']);
    }

    /**
     * @test
     */
    public function assert_json_body_subset_succeeds_when_the_given_array_is_a_subset_of_the_body()
    {
        $request = new Request(new GuzzleRequest('get', '/test', [], '{"key1":"value","key2":"value"}'));
        $request->assertJsonBodySubset(['key1' => 'value']);
        $request->assertJsonBodySubset(['key2' => 'value']);
    }

    /**
     * @test
     */
    public function assert_json_body_subset_fails_when_the_given_array_is_not_a_subset_of_the_body()
    {
        $request = new Request(new GuzzleRequest('get', '/test', [], '{"key1":"value","key2":"value"}'));
        $this->expectException(ExpectationFailedException::class);
        $request->assertJsonBodySubset(['key1' => 'invalid']);
    }

    /**
     * @test
     */
    public function assert_json_body_has_key_succeeds_when_the_key_exists_in_the_body()
    {
        $request = new Request(new GuzzleRequest('get', '/test', [], '{"key1":"value","key2":"value"}'));
        $request->assertJsonBodyHasKey('key1');
        $request->assertJsonBodyHasKey('key2');
    }

    /**
     * @test
     */
    public function assert_json_body_has_key_correctly_traverses_nested_keys()
    {
        $request = new Request(new GuzzleRequest('get', '/test', [], '{"key":{"inner":"value"}}'));
        $request->assertJsonBodyHasKey('key.inner');
    }

    /**
     * @test
     */
    public function assert_json_body_has_key_fails_when_the_key_does_not_exist_in_the_body()
    {
        $request = new Request(new GuzzleRequest('get', '/test', [], '{"key":"value"}'));
        $this->expectException(ExpectationFailedException::class);
        $request->assertJsonBodyHasKey('invalid');
    }

    /**
     * @test
     */
    public function assert_json_body_contains_succeeds_when_the_key_and_value_combination_exists_in_the_body()
    {
        $request = new Request(new GuzzleRequest('get', '/test', [], '{"key1":"value1","key2":"value2"}'));
        $request->assertJsonBodyContains('key1', 'value1');
        $request->assertJsonBodyContains('key2', 'value2');
    }

    /**
     * @test
     */
    public function assert_json_body_contains_correctly_traverses_nested_keys()
    {
        $request = new Request(new GuzzleRequest('get', '/test', [], '{"key":{"inner":"value"}}'));
        $request->assertJsonBodyContains('key.inner', 'value');
    }

    /**
     * @test
     */
    public function assert_json_body_contains_fails_when_the_key_and_value_combination_does_not_exist_in_the_body()
    {
        $request = new Request(new GuzzleRequest('get', '/test', [], '{"key":"value"}'));
        $this->expectException(ExpectationFailedException::class);
        $request->assertJsonBodyContains('key', 'invalid');
    }
}
