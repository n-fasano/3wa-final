<?php

namespace App\Controller\Dto;

use App\Entity\User;

class UserView implements DataTransferObject
{
    public function __construct(User $user)
    {
        $this->id = $user->id;
        $this->username = $user->username;
    }

    public int $id;
    public string $username;
}