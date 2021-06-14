<?php

namespace App\Repository;

use App\Entity\Entity;
use App\Entity\Metadata\OneToMany;
use App\Entity\Metadata\ProxyBuilder;
use App\Entity\Metadata\ProxyCollection;
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
    protected function _getAll(string $class): array
    {
        $reader = new Reader($class);
        $table = EntitySerializer::serialize($reader->shortName());
        $select = new Select($table);

        $results = Connection::query($select);
        
        return array_map(
            fn($row) => $this->_hydrate($class, $row),
            $results
        );
    }

    protected function _get(string $class, int $id): ?Entity
    {
        $reader = new Reader($class);
        $table = EntitySerializer::serialize($reader->shortName());
        $select = new Select(
            $table,
            new Where(new Search(
                [new Criteria(
                    'id', 
                    $id
                )]
            ))
        );

        $results = Connection::query($select);
        $row = array_pop($results);
        
        return null === $row ? null : $this->_hydrate($class, $row);
    }

    protected function _exists(string $class, int $id): bool
    {
        $reader = new Reader($class);
        $table = EntitySerializer::serialize($reader->shortName());
        $select = new Select(
            $table,
            new Where(new Search(
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
    protected function _findAll(string $class, Search $search): array
    {
        $reader = new Reader($class);
        $table = EntitySerializer::serialize($reader->shortName());
        $select = new Select(
            $table,
            new Where($search)
        );

        $results = Connection::query($select);
        
        return array_map(
            fn($row) => $this->_hydrate($class, $row),
            $results
        );
    }
    
    protected function _find(string $class, Search $search): ?Entity
    {
        $reader = new Reader($class);
        $table = EntitySerializer::serialize($reader->shortName());
        $select = new Select(
            $table,
            new Where($search)
        );

        $results = Connection::query($select);
        $row = array_pop($results);
        
        return null === $row ? null : $this->_hydrate($class, $row);
    }
    
    protected function _create(Entity $entity): bool
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

        if (true === $success = Connection::command($sql, $sqlParameters)) {
            $entity->id = Connection::lastInsertId();
        }
        
        return $success;
    }
    
    protected function _update(Entity $entity): bool
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
    
    protected function _delete(Entity $entity): bool
    {
        $class = get_class($entity);
        $table = EntitySerializer::serialize($class);

        $sql = "DELETE FROM $table WHERE id = :id";
        return Connection::command($sql, [':id' => $entity->id]);
    }

    protected function _hydrate(string $class, array $row)
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
                $value = $this->_getProxy($type, $value);
            }
            else if ('iterable' === $type) {
                $value = $this->_getProxyCollection($property, $reader->shortName(), $row['id']);
            }

            $entity->{$field} = $value;
        }

        return $entity;
    }

    protected function _getProxy(string $type, int $id)
    {
        $repository = $this;
        $proxy = ProxyBuilder::getProxy($type);
        return new $proxy(function () use ($repository, $type, $id) {
            static $instance;
            if (!isset($instance)) {
                $instance = $repository->_get($type, $id);
            }
            return $instance;
        });
    }

    protected function _getProxyCollection(ReflectionProperty $property, string $parentClass, int $parentId)
    {
        $attribute = $property->getAttributes(OneToMany::class)[0];
        $oneToMany = $attribute->newInstance();
        $childClass = $oneToMany->class;

        $table = 
            EntitySerializer::serialize($parentClass) . '_' . 
            EntitySerializer::serialize($childClass);

        return new ProxyCollection(function () use ($table, $parentClass, $childClass, $parentId) {
            return Connection::query(
                new Select(
                    $table,
                    new Where(
                        new Search(
                            $childClass,
                            [new Criteria(
                                FieldSerializer::serializeId($parentClass), $parentId
                            )]
                        )
                    )
                )
            );
        }, $parentClass, $oneToMany->class, $parentId);
    }
}