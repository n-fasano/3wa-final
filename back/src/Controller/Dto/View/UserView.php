<?php

namespace App\Controller\Dto\View;

use App\Entity\User;

class UserView
{
    public function __construct(User $user)
    {
        $this->id = $user->id;
        $this->username = $user->username;
    }

    public int $id;
    public string $username;
}