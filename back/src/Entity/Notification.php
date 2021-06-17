<?php

namespace App\Entity;

class Notification extends Entity
{
    protected User $user;
    protected Thread $thread;
    protected int $count;
    protected Message $lastMessageRead;
}