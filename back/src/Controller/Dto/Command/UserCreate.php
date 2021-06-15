<?php

namespace App\Controller\Dto\Command;

use App\Controller\Dto\DataTransferObject;
use App\Controller\Dto\Constraint\Length;

class UserCreate implements DataTransferObject
{
    #[Length(4, 32)]
    private string $username;

    #[Length(8, 255)]
    private string $password;

    public function username(): string
    {
        return $this->username;
    }

    public function password(): string
    {
        return $this->password;
    }
}