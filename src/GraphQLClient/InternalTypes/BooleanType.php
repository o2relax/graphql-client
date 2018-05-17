<?php

namespace GraphQLClient\InternalTypes;

use GraphQLClient\InternalType;

class BooleanType extends InternalType
{

    /**
     * BooleanType constructor.
     *
     * @param string $name
     * @param array  $children
     */
    public function __construct(string $name, array $children = [])
    {
        parent::__construct($name, $children);
    }

}
