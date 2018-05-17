<?php

namespace GraphQLClient\InternalTypes;

use GraphQLClient\InternalType;

class IntegerType extends InternalType
{

    /**
     * IntegerType constructor.
     *
     * @param string $name
     * @param array  $children
     */
    public function __construct(string $name, array $children = [])
    {
        parent::__construct($name, $children);
    }

}
