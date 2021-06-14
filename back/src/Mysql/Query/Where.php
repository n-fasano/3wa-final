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
                case Criteria::TYPE_SIMILAR:
                    $value = "%$value%";
                    $symbol = "LIKE";
                    break;
                case Criteria::TYPE_INFERIOR:
                    $symbol = "<";
                    break;
                case Criteria::TYPE_SUPERIOR:
                    $symbol = ">";
                    break;
                case Criteria::TYPE_NOT_EQUAL:
                    $symbol = "!=";
                    break;
                
                case Criteria::TYPE_EQUAL:
                default:
                    $symbol = '=';
                    break;
            }

            $this->query .= " AND $sqlField $symbol $token";
            $this->parameters[$token] = $value;
        }
    }
}