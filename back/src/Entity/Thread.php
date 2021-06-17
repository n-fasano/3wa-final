<?php

namespace App\Entity;

use App\Entity\Metadata\ManyToMany;
use App\Entity\Metadata\OneToMany;

class Thread extends Entity
{
    #[ManyToMany(User::class)]
    protected iterable $users = [];

    #[OneToMany(Message::class)]
    protected iterable $messages = [];

    public function hasUser(User $user): bool
    {
        foreach ($this->users as $threadUser) {
            if ($threadUser->id === $user->id) {
                return true;
            }
        }

        return false;
    }
}