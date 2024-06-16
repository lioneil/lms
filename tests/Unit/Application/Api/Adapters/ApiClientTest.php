<?php

namespace Tests\Unit\Application\Api\Adapters;

use Core\Application\Api\Adapters\ApiClient;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * @package Customer\Unit\Services\Crm
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ApiClientTest extends TestCase
{
    /**
     * @test
     * @group  unit
     * @group  unit:api
     * @return void
     */
    public function it_can_return_the_response_object_when_calling_a_get_request()
    {
        // Arrangements
        // Mock the requests to be sent to Guzzle.
        $mock = new MockHandler([
            new GuzzleResponse(Response::HTTP_OK, ['X-Foo' => 'Bar'], 'Hello, World'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new ApiClient(new Client(['handler' => $handlerStack]));

        // Actions
        // Note: make sure to have APP_URL equal
        // to whatever host:port you are running.
        $response = $client->get('/', ['auth' => 'user', 'pass']);

        $this->assertInstanceOf(GuzzleResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:api
     * @return void
     */
    public function it_can_return_the_response_object_when_calling_a_post_request()
    {
        // Arrangements
        // Mock the requests to be sent to Guzzle.
        $mock = new MockHandler([
            new GuzzleResponse(Response::HTTP_OK, ['X-Foo' => 'Bar'], 'Hello, World'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new ApiClient(new Client(['handler' => $handlerStack]));

        // Actions
        // Note: make sure to have APP_URL equal
        // to whatever host:port you are running.
        $response = $client->post('/', ['auth' => 'user', 'pass']);

        $this->assertInstanceOf(GuzzleResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:api
     * @return void
     */
    public function it_can_return_the_response_object_when_calling_a_put_request()
    {
        // Arrangements
        // Mock the requests to be sent to Guzzle.
        $mock = new MockHandler([
            new GuzzleResponse(Response::HTTP_OK, ['X-Foo' => 'Bar'], 'Hello, World'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new ApiClient(new Client(['handler' => $handlerStack]));

        // Actions
        // Note: make sure to have APP_URL equal
        // to whatever host:port you are running.
        $response = $client->put('/', ['auth' => 'user', 'pass']);

        $this->assertInstanceOf(GuzzleResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:api
     * @return void
     */
    public function it_can_return_the_response_object_when_calling_a_patch_request()
    {
        // Arrangements
        // Mock the requests to be sent to Guzzle.
        $mock = new MockHandler([
            new GuzzleResponse(Response::HTTP_OK, ['X-Foo' => 'Bar'], 'Hello, World'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new ApiClient(new Client(['handler' => $handlerStack]));

        // Actions
        // Note: make sure to have APP_URL equal
        // to whatever host:port you are running.
        $response = $client->patch('/', ['auth' => 'user', 'pass']);

        $this->assertInstanceOf(GuzzleResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:api
     * @return void
     */
    public function it_can_return_the_response_object_when_calling_a_delete_request()
    {
        // Arrangements
        // Mock the requests to be sent to Guzzle.
        $mock = new MockHandler([
            new GuzzleResponse(Response::HTTP_OK, ['X-Foo' => 'Bar'], 'Hello, World'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new ApiClient(new Client(['handler' => $handlerStack]));

        // Actions
        // Note: make sure to have APP_URL equal
        // to whatever host:port you are running.
        $response = $client->delete('/', ['auth' => 'user', 'pass']);

        $this->assertInstanceOf(GuzzleResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @test
     * @group  unit
     * @group  unit:api
     * @return void
     */
    public function it_can_directly_call_any_guzzle_method_from_the_api_client()
    {
        // Arrangements
        // Mock the requests to be sent to Guzzle.
        $mock = new MockHandler([
            new GuzzleResponse(Response::HTTP_OK, ['X-Foo' => 'Bar'], 'Hello, World'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new ApiClient(new Client(['handler' => $handlerStack]));

        // Actions
        // Note: make sure to have APP_URL equal
        // to whatever host:port you are running.
        $response = $client->request(Request::METHOD_GET, '/', ['auth' => 'user', 'pass']);

        $this->assertInstanceOf(GuzzleResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
