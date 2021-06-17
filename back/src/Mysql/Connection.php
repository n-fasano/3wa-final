<?php

namespace App\Mysql;

use App\Mysql\Query\Select;
use PDO;

class Connection
{
    public const DATE_FORMAT = 'Y-m-d H:i:s';

    private static PDO $instance;

    private static function getInstance()
    {
        if (!isset(self::$instance)) {
            $dbname = MYSQL_DATABASE;
            $host = MYSQL_HOST;
            $port = MYSQL_PORT;
            $dsn = "mysql:dbname=$dbname;host=$host;port=$port";

            self::$instance = new PDO($dsn, MYSQL_USER, MYSQL_PASSWORD);
        }

        return self::$instance;
    }

    public static function query(Select $select): array
    {
        $statement = self::getInstance()->prepare($select->build());
        $statement->execute($select->getParameters());
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function command(string $sql, array $parameters): bool
    {
        $statement = self::getInstance()->prepare($sql);
        return $statement->execute($parameters);
    }

    public static function lastInsertId(): int
    {
        return (int) self::$instance->lastInsertId();
    }
}