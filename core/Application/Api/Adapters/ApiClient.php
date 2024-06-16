<?php

namespace Core\Application\Api\Adapters;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ApiClient
{
    /**
     * The Guzzle Client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Instantiate the client property.
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Perform a GET request to the specified url.
     *
     * @param  string $url
     * @param  array  $parameters
     * @return \GuzzleHttp\Psr7\Response
     */
    public function get(string $url, array $parameters = [])
    {
        return $this->client->request(Request::METHOD_GET, $url, $parameters);
    }

    /**
     * Perform a POST request to the specified url.
     *
     * @param  string $url
     * @param  array  $parameters
     * @return \GuzzleHttp\Psr7\Response
     */
    public function post(string $url, array $parameters = [])
    {
        return $this->client->request(Request::METHOD_POST, $url, $parameters);
    }

    /**
     * Perform a PUT request to the specified url.
     *
     * @param  string $url
     * @param  array  $parameters
     * @return \GuzzleHttp\Psr7\Response
     */
    public function put(string $url, array $parameters = [])
    {
        return $this->client->request(Request::METHOD_PUT, $url, $parameters);
    }

    /**
     * Perform a PATCH request to the specified url.
     *
     * @param  string $url
     * @param  array  $parameters
     * @return \GuzzleHttp\Psr7\Response
     */
    public function patch(string $url, array $parameters = [])
    {
        return $this->client->request(Request::METHOD_PATCH, $url, $parameters);
    }

    /**
     * Perform a DELETE request to the specified url.
     *
     * @param  string $url
     * @param  array  $parameters
     * @return \GuzzleHttp\Psr7\Response
     */
    public function delete(string $url, array $parameters = [])
    {
        return $this->client->request(Request::METHOD_DELETE, $url, $parameters);
    }

    /**
     * Call the model's method magically.
     *
     * @param  string $method
     * @param  array  $attributes
     * @return mixed
     */
    public function __call(string $method, array $attributes)
    {
        return call_user_func_array([$this->client, $method], $attributes);
    }
}
