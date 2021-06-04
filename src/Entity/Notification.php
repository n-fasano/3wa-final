<?php

namespace App\Entity;

class Notification extends Entity
{
    public User $user;
    public Message $message;
}