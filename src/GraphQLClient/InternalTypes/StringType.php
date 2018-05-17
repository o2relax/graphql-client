<?php

namespace GraphQLClient\InternalTypes;

use GraphQLClient\InternalType;

class StringType extends InternalType
{

    /**
     * StringType constructor.
     *
     * @param string $name
     * @param array  $children
     */
    public function __construct(string $name, array $children = [])
    {
        parent::__construct($name, $children);
    }

}
