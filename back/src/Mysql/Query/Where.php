<?php

namespace App\Mysql\Query;

use App\Repository\Search\Criteria;
use App\Repository\Search\Search;

class Where
{
    public string $query = '1';
    public array $parameters;

    public function __construct(Search $search)
    {
        /** @var Criteria $criteria */
        foreach ($search as $criteria) {
            $sqlField = $criteria->field;
            $token = ":$sqlField";
            $value = $criteria->value;
            
            switch ($criteria->type) {
                case Criteria::TYPE_IN:
                    $this->buildIn($sqlField, $value);
                    break;

                case Criteria::TYPE_SIMILAR:
                    $this->build($sqlField, 'LIKE', $token, "%$value%");
                    break;
                case Criteria::TYPE_INFERIOR:
                    $this->build($sqlField, '<', $token, $value);
                    break;
                case Criteria::TYPE_SUPERIOR:
                    $this->build($sqlField, '>', $token, $value);
                    break;
                case Criteria::TYPE_NOT_EQUAL:
                    $this->build($sqlField, '!=', $token, $value);
                    break;
                case Criteria::TYPE_EQUAL:
                default:
                    $this->build($sqlField, '=', $token, $value);
                    break;
            }
        }
    }

    protected function build($sqlField, $symbol, $token, $value)
    {
        $this->query .= " AND $sqlField $symbol $token";
        $this->parameters[$token] = $value;
    }

    protected function buildIn($sqlField, array $value)
    {
        $tokens = '';
        foreach ($value as $i => $subvalue) {
            $tokens .= ":$sqlField$i,";
            $this->parameters[":$sqlField$i"] = $subvalue;
        }
        $tokens = rtrim($tokens, ',');

        $this->query .= " AND $sqlField IN ($tokens)";
    }
}