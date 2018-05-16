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
     * @return array|null
     */
    public function getData() : ?array
    {
        return array_get($this->response, 'data.' . $this->query->getName());
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
        return array_get($this->response, 'errors.0');
    }

    /**
     * @return array|null
     */
    public function getCode() : ?array
    {
        return array_get($this->response, 'errors.0.code');
    }
}
