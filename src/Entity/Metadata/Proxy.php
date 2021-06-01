<?php

namespace App\Entity\Metadata;

use App\Service\StringCollection;
use ReflectionClass;

class Proxy
{
    private ReflectionClass $reflection;
    private StringCollection $fields;

    public function __construct(string $entity)
    {
        $this->reflection = new ReflectionClass($entity);
    }

    /** @return StringCollection<string> */
    public function fields(): StringCollection
    {
        return $this->fields ??= new StringCollection(
            array_map(
                fn ($prop) => $prop->getName(), 
                $this->reflection->getProperties()
            )
        );
    }

    public function getter(string $field): string
    {
        return 'get'.ucfirst($field);
    }

    public function setter(string $field): string
    {
        return 'set'.ucfirst($field);
    }
}