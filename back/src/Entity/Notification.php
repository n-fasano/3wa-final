<?php

namespace App\Entity;

class Notification extends Entity
{
    public User $user;
    public Thread $thread;
    public int $count;
    public Message $lastMessageRead;
}