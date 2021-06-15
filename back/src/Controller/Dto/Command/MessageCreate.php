<?php

namespace App\Controller\Dto\Command;

use App\Controller\Dto\DataTransferObject;
use App\Controller\Dto\Constraint\Exists;
use App\Controller\Dto\Constraint\NotBlank;
use App\Repository\ThreadRepository;

class MessageCreate implements DataTransferObject
{
    #[NotBlank, Exists(ThreadRepository::class)]
    private int $thread;

    #[NotBlank]
    private string $content;

    public function thread(): int
    {
        return $this->thread;
    }

    public function content(): string
    {
        return $this->content;
    }
}