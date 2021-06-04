<?php

namespace App\Repository;

use App\Entity\Entity;
use App\Entity\Metadata\Proxy;
use App\Entity\Metadata\Reader;
use App\Mysql\Connection;
use App\Mysql\EntitySerializer;
use App\Mysql\FieldSerializer;
use App\Mysql\Query\Select;
use App\Mysql\Query\Where;
use App\Repository\Search\Criteria;
use App\Repository\Search\Search;
use ReflectionProperty;

abstract class Repository
{
    /** @return array<Entity> */
    public function getAll(string $class): array
    {
        $reader = new Reader($class);
        $table = EntitySerializer::serialize($reader->shortName());
        $select = new Select($table);

        $results = Connection::query($select);
        
        return array_map(
            fn($row) => $this->hydrate($class, $row),
            $results
        );
    }

    public function get(string $class, int $id): ?Entity
    {
        $reader = new Reader($class);
        $table = EntitySerializer::serialize($reader->shortName());
        $select = new Select(
            $table,
            new Where(new Search(
                $class,
                [new Criteria(
                    'id', 
                    $id
                )]
            ))
        );

        $results = Connection::query($select);
        $row = array_pop($results);
        
        return null === $row ? null : $this->hydrate($class, $row);
    }

    public function exists(string $class, int $id): bool
    {
        $reader = new Reader($class);
        $table = EntitySerializer::serialize($reader->shortName());
        $select = new Select(
            $table,
            new Where(new Search(
                $class,
                [new Criteria(
                    'id', 
                    $id
                )]
            ))
        );

        $results = Connection::query($select);
        $row = array_pop($results);
        
        return null !== $row;
    }

    /** @return array<Entity> */
    public function findAll(Search $search): array
    {
        $reader = new Reader($search->class);
        $table = EntitySerializer::serialize($reader->shortName());
        $select = new Select(
            $table,
            new Where($search)
        );

        $results = Connection::query($select);
        
        return array_map(
            fn($row) => $this->hydrate($search->class, $row),
            $results
        );
    }
    
    public function find(Search $search): ?Entity
    {
        $reader = new Reader($search->class);
        $table = EntitySerializer::serialize($reader->shortName());
        $select = new Select(
            $table,
            new Where($search)
        );

        $results = Connection::query($select);
        $row = array_pop($results);
        
        return null === $row ? null : $this->hydrate($search->class, $row);
    }
    
    public function create(Entity $entity, ...$parameters): bool
    {
        $class = get_class($entity);
        $reader = new Reader($class);
        $table = EntitySerializer::serialize($reader->shortName());

        $sqlFields = '';
        $sqlValues = '';
        $sqlParameters = [];
        
        $fields = $reader->fields()->not('id');
        foreach ($fields as $field) {
            if (null !== $fieldValue = $entity->{$field}) {
                $sqlField = FieldSerializer::serialize($field);
                $sqlFields .= "$sqlField,";

                if (is_a($fieldValue, Entity::class)) {
                    $fieldValue = $fieldValue->getId();
                }

                $token = ":$sqlField";
                $sqlValues .= "$token,";

                $sqlParameters[$token] = $fieldValue;
            }
        }

        $sqlFields = rtrim($sqlFields, ',');
        $sqlValues = rtrim($sqlValues, ',');

        $sql = "INSERT INTO $table ($sqlFields) VALUES ($sqlValues)";
        return Connection::command($sql, $sqlParameters);
    }
    
    public function update(Entity $entity): bool
    {
        $class = get_class($entity);
        $reader = new Reader($class);
        $table = EntitySerializer::serialize($reader->shortName());

        $sqlAssignments = '';
        $sqlParameters = [];
        
        foreach ($reader->fields()->not('id') as $field) {
            if (null !== $fieldValue = $entity->{$field}) {
                $sqlField = FieldSerializer::serialize($field);
                $token = ":$sqlField";

                if (is_a($fieldValue, Entity::class)) {
                    $fieldValue = $fieldValue->getId();
                }

                $sqlAssignments .= "$sqlField = $token,";
                $sqlParameters[$token] = $fieldValue;
            }
        }

        $sqlAssignments = rtrim($sqlAssignments, ',');
        $sqlParameters[':id'] = $entity->id;

        $sql = "UPDATE $table SET $sqlAssignments WHERE id = :id";
        return Connection::command($sql, $sqlParameters);
    }
    
    public function delete(Entity $entity): bool
    {
        $class = get_class($entity);
        $table = EntitySerializer::serialize($class);

        $sql = "DELETE FROM $table WHERE id = :id";
        return Connection::command($sql, [':id' => $entity->id]);
    }

    public function hydrate(string $class, array $row)
    {
        $reader = new Reader($class);
        $entity = new $class;

        /** @var ReflectionProperty $property */
        foreach ($reader->properties() as $property) {
            $field = $property->getName();
            $type = $property->getType();

            $sqlField = FieldSerializer::serialize($field);
            $value = $row[$sqlField];

            if (is_a($type, Entity::class)) {
                $repository = $this;
                $value = new Proxy(function () use ($repository, $type, $value) {
                    static $instance;
                    if (!isset($instance)) {
                        $instance = $repository->get($type, $value);
                    }
                    return $instance;
                });
            }

            $entity->{$field} = $value;
        }

        return $entity;
    }
}