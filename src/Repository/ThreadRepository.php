<?php

namespace App\Repository;

use App\Entity\Entity;

class ThreadRepository extends Repository
{
    public function create(Entity $thread, ...$parameters): bool 
    {
        $usersIds = $parameters['usersIds'] ?? [];
        

        
        return false;
    }
}