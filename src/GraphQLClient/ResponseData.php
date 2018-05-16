<?php

namespace GraphQLClient;

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
        return array_get($this->response, 'data.' . $this->query->getName());
    }

    public function getErrors()
    {
        return array_get($this->response, 'errors.0');
    }

    public function getCode()
    {
        return array_get($this->response, 'errors.0.code');
    }
}
