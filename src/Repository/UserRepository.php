<?php

namespace App\Repository;

use App\Entity\User;

class UserRepository extends Repository
{
    public function findByUsername(string $username): ?User 
    {
        return new User;
    }
}