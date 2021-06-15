<?php

namespace App\Controller\Dto\Query;

use App\Controller\Dto\Constraint\Exists;
use App\Controller\Dto\DataTransferObject;
use App\Repository\ThreadRepository;

class ThreadMessagesQuery implements DataTransferObject
{
    #[Exists(ThreadRepository::class)]
    private int $id;

    private int $pageSize = 25;
    private int $page = 1;

    public function id(): int
    {
        return $this->id;
    }

    public function pageSize(): int
    {
        return $this->pageSize;
    }

    public function page(): int
    {
        return $this->page;
    }
}