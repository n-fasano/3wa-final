<?php

namespace App\Controller\Dto\Command;

use App\Controller\Dto\DataTransferObject;
use App\Controller\Dto\Constraint\Exists;
use App\Controller\Dto\Constraint\Length;
use App\Controller\Dto\Constraint\NotBlank;
use App\Repository\ThreadRepository;
use DateTimeInterface;

class MessageCreate implements DataTransferObject
{
    public const DATE_FORMAT = 'Y-m-d H:i:s';
    
    #[Exists(ThreadRepository::class)]
    private int $thread;

    #[Length(1, 200)]
    private string $content;

    private DateTimeInterface $sentAt;

    public function thread(): int
    {
        return $this->thread;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function sentAt(): DateTimeInterface
    {
        return $this->sentAt;
    }
}