<?php

namespace App\Repository;

use App\Entity\Entity;
use App\Entity\Metadata\ManyToMany;
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
use DateTime;
use DateTimeInterface;
use Exception;
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

    protected static $_getCache = [];

    protected function _get(string $class, int $id): ?Entity
    {
        $key = "$class-$id";

        if (!isset($_getCache[$key])) {
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
            
            $_getCache[$key] = null === $row ? null : $this->_hydrate($class, $row);
        }

        return $_getCache[$key];
    }

    abstract public function exists(int $id): bool;

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
    protected function _findAll(string $class, Search $search, int $pageSize = 0, int $page = 1): array
    {
        $reader = new Reader($class);
        $table = EntitySerializer::serialize($reader->shortName());
        $select = new Select(
            $table,
            new Where($search),
            ['*'],
            $pageSize,
            $pageSize * ($page - 1)
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
                if (is_array($fieldValue)) {
                    continue;
                }

                $sqlField = FieldSerializer::serialize($field);
                if (is_a($fieldValue, Entity::class)) {
                    $sqlField = FieldSerializer::serializeId(get_class($fieldValue));
                    $fieldValue = $fieldValue->id;
                }
                else if (is_a($fieldValue, DateTimeInterface::class)) {
                    $fieldValue = $fieldValue->format(Connection::DATE_FORMAT);
                }

                $sqlFields .= "$sqlField,";

                $token = ":$sqlField";
                $sqlValues .= "$token,";

                $sqlParameters[$token] = $fieldValue;
            }
        }

        $sqlFields = rtrim($sqlFields, ',');
        $sqlValues = rtrim($sqlValues, ',');

        $sql = "INSERT INTO $table ($sqlFields) VALUES ($sqlValues);";

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

        $sql = "UPDATE $table SET $sqlAssignments WHERE id = :id;";
        return Connection::command($sql, $sqlParameters);
    }
    
    protected function _delete(Entity $entity): bool
    {
        $class = get_class($entity);
        $table = EntitySerializer::serialize($class);

        $sql = "DELETE FROM $table WHERE id = :id;";
        return Connection::command($sql, [':id' => $entity->id]);
    }

    protected function _hydrate(string $class, array $row)
    {
        $reader = new Reader($class);
        $entity = new $class;

        /** @var ReflectionProperty $property */
        foreach ($reader->properties() as $property) {
            $field = $property->getName();
            $type = $property->getType()->getName();

            $sqlField = FieldSerializer::serialize($field);
            $value = $row[$sqlField] ?? null;

            if (is_subclass_of($type, Entity::class)) {
                $sqlField = FieldSerializer::serializeId($field);
                if (null !== $value = $row[$sqlField]) {
                    $value = $this->_getProxy($type, (int) $value);
                }
            }
            else if ('iterable' === $type) {
                $value = $this->_getProxyCollection($property, $reader->shortName(), $row['id']);
            }
            else if ($type === DateTimeInterface::class) {
                $value = DateTime::createFromFormat(Connection::DATE_FORMAT, $value) ?: null;
            }

            $entity->{$field} = $value;
        }

        return $entity;
    }

    protected function _getProxy(string $type, int $id)
    {
        $repository = $this;
        $getter = function () use ($repository, $type, $id) {
            static $instance;
            if (!isset($instance)) {
                $instance = $repository->_get($type, $id);
            }
            return $instance;
        };

        // $proxy = ProxyBuilder::getProxy($type);
        // return new $proxy($id, $getter);
        return (new $type)->_toProxy($id, $getter);
    }

    protected function _getProxyCollection(ReflectionProperty $property, string $parentClass, int $parentId)
    {
        $toMany = [
            OneToMany::class => '_getOneToManyProxyCollection', 
            ManyToMany::class => '_getManyToManyProxyCollection'
        ];

        foreach ($toMany as $attrType => $method) {
            $attributes = $property->getAttributes($attrType);
            if (0 < count($attributes)) {
                $attribute = array_shift($attributes);
                $instance = $attribute->newInstance();
                return $this->{$method}($instance, $parentClass, $parentId);
            }
        }

        throw new Exception('Entity property of type iterable is missing ToMany attribute !');
    }

    protected function _getOneToManyProxyCollection(OneToMany $oneToMany, string $parentClass, int $parentId)
    {
        $childClass = $oneToMany->class;
        $parentIdField = FieldSerializer::serializeId($parentClass);

        $repository = $this;
        return new ProxyCollection(function () use ($repository, $childClass, $parentIdField, $parentId) {
            static $items;
            if (!isset($items)) {
                $items = $repository->_findAll($childClass, new Search([
                    new Criteria($parentIdField, $parentId, Criteria::TYPE_EQUAL)
                ]));
            }
            return $items;
        });
    }

    protected function _getManyToManyProxyCollection(ManyToMany $manyToMany, string $parentClass, int $parentId)
    {
        $childClass = $manyToMany->class;
        $childShortName = EntitySerializer::serialize($childClass);

        $table = 
            EntitySerializer::serialize($parentClass) . '_' . 
            $childShortName;

        $field = FieldSerializer::serializeId($parentClass);
        $childIdField = FieldSerializer::serializeId($childShortName);
        $select = new Select($table, new Where(
            new Search([
                new Criteria($field, $parentId)
            ])
        ), [$childIdField]);

        $repository = $this;
        return new ProxyCollection(function (int $limit = 25) use ($select, $repository, $childClass, $childIdField) {
            static $items;
            if (!isset($items)) {
                $results = Connection::query($select);
                $ids = array_column($results, $childIdField);
                $items = $repository->_findAll($childClass, new Search([
                    new Criteria('id', $ids, Criteria::TYPE_IN)
                ]), $limit);
            }
            return $items;
        });
    }
}