<?php


namespace GraphQLClient;


class Field
{
    /** @var Field[]|array */
    protected $children;

    /** @var string */
    protected $name;

    /**
     * Field constructor.
     *
     * @param string $name
     * @param Field[]|array $children
     */
    public function __construct(string $name, array $children = [])
    {
        $this->name = $name;
        $this->children = $children;
    }

    /**
     * @return Field[]|array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChild(Field $field): Field
    {
        $children []= $field;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
