<?php

namespace App\Entity\Metadata;

use App\Entity\Entity;
use App\Service\StringCollection;
use ReflectionClass;

class Reader extends Entity
{
    private ReflectionClass $reflection;
    private StringCollection $fields;

    public function __construct(string $objectOrClass)
    {
        $this->reflection = new ReflectionClass($objectOrClass);
    }

    public function shortName(): string
    {
        return $this->reflection->getShortName();
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

    public function properties(): array
    {
        return $this->reflection->getProperties();
    }
}