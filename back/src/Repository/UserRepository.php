<?php

namespace App\Repository;

use App\Entity\User;
use App\Repository\Search\Criteria;
use App\Repository\Search\Search;

class UserRepository extends Repository
{
    /** @return array<User> */
    public function findAll(array $criterias): array 
    {
        return $this->_findAll(User::class, new Search($criterias));
    }
    
    public function findByUsername(string $username): ?User 
    {
        return $this->_find(
            User::class,
            new Search(
                [
                    new Criteria(
                        'username',
                        $username,
                    )
                ]
            )
        );
    }

    public function create(User $user): bool
    {
        return $this->_create($user);
    }

    public function get(int $id): ?User
    {
        return $this->_get(User::class, $id);
    }
}