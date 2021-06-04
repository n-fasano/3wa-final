<?php

namespace App\Entity;

use App\Entity\Metadata\ManyToMany;

class Thread extends Entity
{
    #[ManyToMany(User::class)]
    public iterable $users;
}