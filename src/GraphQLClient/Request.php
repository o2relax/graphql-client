<?php

namespace GraphQLClient;

use GraphQLClient\InternalTypes\BooleanType;
use GraphQLClient\InternalTypes\IntegerType;
use GraphQLClient\InternalTypes\StringType;
use Laravel\Lumen\Testing\TestCase;
use PHPUnit\Framework\Assert;

abstract class Request
{
    /** @var string */
    protected $baseUrl;

    /** @var array */
    protected $variables;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->variables = [];
    }

    public function query(array $queries, array $headers = []) : Response
    {
        $response = $this->executeQuery($this->getQueryData($queries), $headers);

        return new Response($response, $queries);
    }

    public function mutate(Query $query, array $headers = [], array $multipart = null) : Response
    {
        $response = $this->executeQuery($this->getMutationData($query), $headers, $multipart);

        return new Response($response, $query);
    }

    public function executeQuery(array $data, array $headers = [], array $multipart = null) : array
    {
        if (\is_array($multipart)) {
            $data = array_merge(['operations' => json_encode($data)], $multipart);
        }

        return $this->postQuery($data, $headers);
    }

    private function getQueryData(array $queries) : array
    {

        $fieldsString = '';

        foreach($queries as $query) {
            $fieldsString .= $this->getQueryString($query);
        }

        $queryString = 'query { ' . $fieldsString . ' }';

        return [
            'query'     => $queryString,
            'variables' => null,
        ];
    }

    private function getMutationData(Query $query) : array
    {
        $queryBody = $this->getQueryString($query);
        $queryString = sprintf(
            'mutation %s { %s }',
            $query->getQueryHeader($this->variables),
            $queryBody
        );

        return [
            'query'     => $queryString,
            'variables' => $this->getVariableContent($this->variables),
        ];
    }

    /**
     * @param array $data
     *
     * @param array $headers
     *
     * @return array
     * @throws GraphQLException
     */
    protected function postQuery(array $data, array $headers = []) : array
    {
        $this->post($this->getBaseUrl(), $data, $headers);

        if ($this->response->getStatusCode() >= 400) {
            throw new GraphQLException(sprintf(
                'Mutation failed with status code %d and error %s',
                $this->response->getStatusCode(),
                $this->response->getContent()
            ));
        }

        return json_decode($this->response->getContent(), true);
    }

    /**
     * @return string
     */
    public function getBaseUrl() : string
    {
        return $this->baseUrl;
    }



    private function getQueryString(Field $query) : string
    {
        $fieldString = '';

        if ($query->getChildren()) {
            $fieldString .= '{';
            foreach ($query->getChildren() as $field) {
                $fieldString .= sprintf('%s', $this->getQueryString($field));
                $fieldString .= PHP_EOL;
            }
            $fieldString .= '}';
        }

        $paramString = '';
        if ($query instanceof Query && \count($query->getParams())) {
            $paramString = '(' . $this->getParamString($query->getParams()) . ')';
        }

        return sprintf('%s%s %s', $query->getName(), $paramString, $fieldString);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    private function getParamString(array $params) : string
    {
        $result = '';

        foreach ($params as $key => $value) {
            if (\is_string($key)) {
                $result .= $key . ' : ';
            }
            if (\is_array($value)) {
                if ($this->hasStringKeys($value)) {
                    $result .= sprintf('{ %s } ', $this->getParamString($value));
                } else {
                    $result .= sprintf('[ %s ] ', $this->getParamString($value));
                }
            } else {
                if ($value instanceof Variable) {
                    $result .= sprintf('$%s ', $value->getName());
                    $this->variables[$value->getName()] = $value;
                } else {
                    $result .= sprintf('%s ', json_encode($value));
                }
            }
        }

        return $result;
    }

    private function hasStringKeys(array $array) : bool
    {
        return \count(array_filter(array_keys($array), '\is_string')) > 0;
    }

    /**
     * @param array|Variable[] $variables
     *
     * @return array
     */
    private function getVariableContent(array $variables) : array
    {
        $result = [];

        foreach ($variables as $variable) {
            $result[$variable->getName()] = $variable->getValue();
        }

        return $result;
    }



    public function assertGraphQlFields(array $fields, Query $query) : void
    {
        foreach ($query->getChildren() as $field) {
            $this->assertFieldInArray($field, $fields);
        }
    }

    protected function assertFieldInArray(Field $field, array $result) : void
    {
        if ($this->hasStringKeys($result)) {
            Assert::assertArrayHasKey($field->getName(), $result);
            if ($result[$field->getName()] !== null) {
                if ($field->getChildren()) {
                    foreach ($field->getChildren() as $child) {
                        $this->assertFieldInArray($child, $result[$field->getName()]);
                    }
                } else {
                    $this->assertInternalType($field, $result[$field->getName()]);
                }
            }
        } else {
            foreach ($result as $element) {
                $this->assertFieldInArray($field, $element);
            }
        }
    }

    protected function assertInternalType(Field $field, $element) : void
    {
        if ($field instanceof InternalType) {
            if ($field instanceof IntegerType) {
                TestCase::assertInternalType('integer', $element);
            } elseif ($field instanceof StringType) {
                TestCase::assertInternalType('string', $element);
            } elseif ($field instanceof BooleanType) {
                TestCase::assertInternalType('boolean', $element);
            }
        }
    }

    private function fieldToString(Field $field) : string
    {
        $result = $field->getName();

        if (!empty($field->getChildren())) {
            $children = '';
            foreach ($field->getChildren() as $child) {
                $children .= $this->fieldToString($child);
            }
            $result .= sprintf(' { %s }', $children);
        }

        $result .= PHP_EOL;

        return $result;
    }
}
