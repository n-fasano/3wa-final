<?php

namespace App\Repository;

use App\Entity\User;
use App\Repository\Search\Criteria;
use App\Repository\Search\Search;

class UserRepository extends Repository
{
    public function findByUsername(string $username): ?User 
    {
        return $this->find(new Search(
            User::class,
            [
                new Criteria(
                    'username',
                    $username,
                )
            ]
        ));
    }
}