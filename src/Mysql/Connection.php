<?php

namespace App\Mysql;

use PDO;

class Connection
{
    private static PDO $instance;

    public static function get()
    {
        if (null === self::$instance) {
            $dbname = MYSQL_DATABASE;
            $host = MYSQL_HOST;
            $port = MYSQL_PORT;
            $dsn = "mysql:dbname=$dbname;host=$host;port=$port";

            self::$instance = new PDO($dsn, MYSQL_USER);
        }

        return self::$instance;
    }

    public static function query(string $sql, array $parameters = null)
    {
        $statement = self::$instance->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function command(string $sql, array $parameters)
    {
        $statement = self::$instance->prepare($sql);
        return $statement->execute($parameters);
    }
}