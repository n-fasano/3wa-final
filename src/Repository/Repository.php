<?php

namespace App\Repository;

use App\Entity\Entity;
use App\Entity\Metadata\Proxy;
use App\Mysql\Connection;
use App\Mysql\EntitySerializer;
use App\Mysql\FieldSerializer;
use App\Mysql\Query\Where;
use App\Repository\Search\Criteria;
use App\Repository\Search\Search;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class Repository
{
    /** @return array<Entity> */
    public function getAll(string $class): array
    {
        $table = EntitySerializer::serialize($class);

        $sql = "SELECT * FROM $table";
        $results = Connection::query($sql);
        
        return array_map(
            fn($row) => $this->hydrate($class, $row),
            $results
        );
    }

    public function get(string $class, int $id): ?Entity
    {
        $table = EntitySerializer::serialize($class);

        $sql = "SELECT * FROM $table WHERE id = :id";
        $results = Connection::query($sql, [':id' => $id]);

        $row = array_pop($results);
        
        return null === $row ? null :$this->hydrate($class, $row);
    }

    /** @return array<Entity> */
    public function findAll(Search $search): array
    {
        $table = EntitySerializer::serialize($search->class);

        $where = new Where($search);
        $sqlWhere = $where->query;

        $sql = "SELECT * FROM $table WHERE $sqlWhere";
        $results = Connection::query($sql, $where->parameters);
        
        return array_map(
            fn($row) => $this->hydrate($search->class, $row),
            $results
        );
    }
    
    public function find(Search $search): ?Entity
    {
        $table = EntitySerializer::serialize($search->class);

        $where = new Where($search);
        $sqlWhere = $where->query;

        $sql = "SELECT * FROM $table WHERE $sqlWhere";
        $results = Connection::query($sql, $where->parameters);

        $row = array_pop($results);
        
        return null === $row ? null :$this->hydrate($search->class, $row);
    }
    
    public function create(Entity $entity): bool
    {
        $class = get_class($entity);
        $table = EntitySerializer::serialize($class);

        $sqlFields = '';
        $sqlValues = '';
        $sqlParameters = [];
        
        $proxy = new Proxy(get_class($entity));
        foreach ($proxy->fields()->not('id') as $field) {
            $getter = $proxy->getter($field);
            if (null !== $fieldValue = $entity->{$getter}()) {
                $sqlField = FieldSerializer::serialize($field);
                $sqlFields .= "$sqlField,";

                $token = ":$sqlField";
                $sqlValues .= "$sqlField = $token,";

                $sqlParameters[$sqlField] = $fieldValue;
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
        $table = EntitySerializer::serialize($class);

        $sqlAssignments = '';
        $sqlParameters = [];
        
        $proxy = new Proxy($class);
        foreach ($proxy->fields()->not('id') as $field) {
            $getter = $proxy->getter($field);
            if (null !== $fieldValue = $entity->{$getter}()) {
                $sqlField = FieldSerializer::serialize($field);
                $token = ":$sqlField";

                $sqlAssignments .= "$sqlField = $token,";
                $sqlParameters[$token] = $fieldValue;
            }
        }

        $sqlAssignments = rtrim($sqlAssignments, ',');
        $sqlParameters[':id'] = $entity->getId();

        $sql = "UPDATE $table SET $sqlAssignments WHERE id = :id";
        return Connection::command($sql, $sqlParameters);
    }
    
    public function delete(Entity $entity): bool
    {
        $class = get_class($entity);
        $table = EntitySerializer::serialize($class);

        $sql = "DELETE FROM $table WHERE id = :id";
        return Connection::command($sql, [':id' => $entity->getId()]);
    }

    public function hydrate(string $class, string $row)
    {
        $proxy = new Proxy($class);
        $entity = new $class;

        foreach ($proxy->fields() as $field) {
            $setter = $proxy->setter($field);
            $sqlField = FieldSerializer::serialize($field);
            $entity->{$setter}($row[$sqlField]);
        }
    }
}