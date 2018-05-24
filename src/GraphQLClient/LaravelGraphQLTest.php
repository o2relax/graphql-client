<?php

namespace GraphQLClient;

use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\Concerns\MakesHttpRequests;

/**
 * Class LaravelTestGraphQLClient
 *
 * @package parku\AppBundle\Tests\GraphQL
 */
class LaravelGraphQLTest extends Request
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

}