<?php

namespace App\Mysql\Query;

class Select
{
    public function __construct(
        private string $table, 
        private ?Where $where = null,
        private array $fields = ['*'],
        private int $limit = 0,
        private int $offset = 0
    ) { }

    public function build()
    {
        $fields = implode(',', $this->fields);
        $table = $this->table;
        $sqlWhere = $this?->where->query;
        $limit = 0 !== $this->limit ? "LIMIT {$this->limit}" : '';
        $offset = 0 !== $this->offset ? "OFFSET {$this->offset}" : '';

        return "SELECT $fields FROM $table WHERE $sqlWhere $limit $offset;";
    }

    public function getParameters(): array
    {
        return $this?->where->parameters ?? [];
    }
}