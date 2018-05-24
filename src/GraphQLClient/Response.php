<?php

namespace GraphQLClient;

use Illuminate\Support\Arr;

/**
 * Class ResponseData
 *
 * @package GraphQLClient
 */
class Response
{
    /** @var mixed */
    private $response;

    /**
     * Response constructor.
     *
     * @param $response
     */
    public function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @return array|null
     */
    public function getContent() : ?array
    {
        return $this->response;
    }

    /**
     * @return integer|null
     */
    public function getCode() : ?int
    {
        return Arr::get($this->response, 'errors.0.code');
    }

    /**
     * @return mixed
     */
    public function getPaginatedData($queryName)
    {
        return array_get($this->getData($queryName), 'data');
    }

    /**
     * @return mixed
     */
    public function getData($queryName)
    {
        return Arr::get($this->response, 'data.' . $queryName);
    }

    /**
     * @return string
     */
    public function getErrorMessage() : ?string
    {
        return array_get($this->getErrors(), 'message');
    }

    /**
     * @return array|null
     */
    public function getErrors() : ?array
    {
        return Arr::get($this->response, 'errors.0');
    }

    /**
     * @return array
     */
    public function getFailedValidationFields() : ?array
    {
        $responseErrors = $this->getErrors();
        $fields = [];
        if ($responseValidation = array_get($responseErrors, 'validation')) {
            /** @noinspection ForeachSourceInspection */
            foreach ($responseValidation as $field => $error) {
                $fields[$field] = $error;
            }
        }

        return $fields;
    }
}
