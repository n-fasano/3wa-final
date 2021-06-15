<?php

namespace App\Controller\Dto\Command;

use App\Controller\Dto\DataTransferObject;
use App\Controller\Dto\Constraint\ArrayConstraint;
use App\Controller\Dto\Constraint\Exists;
use App\Controller\Dto\Constraint\NotBlank;
use App\Repository\UserRepository;

class ThreadCreate implements DataTransferObject
{
    #[NotBlank, ArrayConstraint(Exists::class, UserRepository::class)]
    private array $users;

    public function users(): array
    {
        return $this->users;
    }
}