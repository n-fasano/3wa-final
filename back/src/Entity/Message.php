<?php

namespace App\Entity;

class Message extends Entity
{
    public User $user;
    public Thread $thread;
    public string $content;
}