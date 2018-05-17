<?php

namespace GraphQLClient;

abstract class InternalType extends Field
{

    /**
     * InternalType constructor.
     *
     * @param string $name
     * @param array  $children
     */
    public function __construct(string $name, $children = [])
    {
        parent::__construct($name, $children);
    }

}
