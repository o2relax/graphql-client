<?php

namespace GraphQLClient;

use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;
use Laravel\Lumen\Application;


/**
 * Class LaravelTestGraphQLClient
 *
 * @package parku\AppBundle\Tests\GraphQL
 */
class LaravelTestGraphQLClient extends Client
{
    use MakesHttpRequests;

    /** @var Application */
    private $app;

    /**
     * WebTestGraphQLClient constructor.
     *
     * @param Application $app
     * @param string      $baseUrl
     */
    public function __construct(Application $app, string $baseUrl)
    {
        parent::__construct($baseUrl);

        $this->app = $app;
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws GraphQLException
     */
    protected function postQuery(array $data): array
    {
        $this->post($this->getBaseUrl(), $data);

        if ($this->response->getStatusCode() >= 400) {
            throw new GraphQLException(sprintf(
                'Mutation failed with status code %d and error %s',
                $this->response->getStatusCode(),
                $this->response->getContent()
            ));
        }

        return json_decode($this->response->getContent(), true);
    }
}