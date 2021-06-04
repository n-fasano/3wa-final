<?php

namespace App\Entity;

use App\Entity\Metadata\OneToMany;

class Thread extends Entity
{
    #[OneToMany(User::class)]
    public array $users;
}