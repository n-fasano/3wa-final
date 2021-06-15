<?php

namespace App\Entity;

use App\Entity\Metadata\ManyToMany;
use App\Entity\Metadata\OneToMany;

class Thread extends Entity
{
    #[ManyToMany(User::class)]
    public iterable $users = [];

    #[OneToMany(Message::class)]
    public iterable $messages = [];
}