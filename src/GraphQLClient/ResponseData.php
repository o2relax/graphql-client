<?php

namespace GraphQLClient;

use Illuminate\Support\Arr;

/**
 * Class ResponseData
 *
 * @package GraphQLClient
 */
class ResponseData
{
    /** @var mixed */
    private $response;
    private $query;

    /**
     * ResponseData constructor.
     *
     * @param       $response
     * @param Query $query
     */
    public function __construct($response, Query $query)
    {
        $this->response = $response;
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return Arr::get($this->response, 'data.' . $this->query->getName());
    }

    /**
     * @return array|null
     */
    public function getResponse() : ?array
    {
        return $this->response;
    }

    /**
     * @return array|null
     */
    public function getErrors() : ?array
    {
        return Arr::get($this->response, 'errors.0');
    }

    /**
     * @return integer|null
     */
    public function getCode() : ?int
    {
        return Arr::get($this->response, 'errors.0.code');
    }
}
