<?php

namespace App\Controller\Dto;

use App\Entity\Thread;
use App\Entity\User;

class ThreadView implements DataTransferObject
{
    public function __construct(Thread $thread)
    {
        /** @var User $user */
        foreach ($thread->users as $user) {
            $this->users[] = new UserView($user);
        }
    }

    public array $users;
}