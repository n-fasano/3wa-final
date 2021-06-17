<?php

namespace App\Controller\Dto\View;

use App\Entity\Message;

class MessageView
{
    public function __construct(Message $message)
    {
        $this->id = $message->id;
        $this->userId = $message->user->id;
        $this->userName = $message->user->username;
        $this->content = $message->content;
        $this->sentAt = $message->sentAt->format('d-m-Y H:i:s');
    }

    public int $id;
    public int $userId;
    public string $userName;
    public string $content;
}