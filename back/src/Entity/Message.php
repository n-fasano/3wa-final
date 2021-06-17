<?php

namespace App\Entity;

use DateTimeInterface;

class Message extends Entity
{
    protected User $user;
    protected Thread $thread;
    protected string $content;
    protected DateTimeInterface $sentAt;
}