<?php

namespace App\Controller\Dto\View;

use App\Entity\Thread;
use App\Entity\User;

class ThreadView
{
    public function __construct(Thread $thread)
    {
        $this->id = $thread->id;

        /** @var User $user */
        foreach ($thread->users as $user) {
            $this->users[] = new UserView($user);
        }
    }

    public int $id;
    public array $users = [];
}