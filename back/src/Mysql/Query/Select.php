<?php

namespace App\Mysql\Query;

class Select
{
    public function __construct(
        private string $table, 
        private ?Where $where = null,
        private array $fields = ['*']
    ) { }

    public function build()
    {
        $fields = implode(',', $this->fields);
        $table = $this->table;
        $sqlWhere = $this?->where->query;

        return "SELECT $fields FROM $table WHERE $sqlWhere";
    }

    public function getParameters(): array
    {
        return $this?->where->parameters ?? [];
    }
}