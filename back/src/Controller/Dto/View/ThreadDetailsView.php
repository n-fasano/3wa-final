<?php

namespace App\Controller\Dto\View;

use App\Entity\Thread;
use App\Entity\Message;

class ThreadDetailsView extends ThreadView
{
    public function __construct(Thread $thread)
    {
        parent::__construct($thread);

        /** @var Message $message */
        foreach ($thread->messages as $message) {
            $this->messages[] = new MessageView($message);
        }
    }

    public array $messages = [];
}