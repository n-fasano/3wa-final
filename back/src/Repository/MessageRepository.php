<?php

namespace App\Repository;

use App\Entity\Message;
use App\Repository\Search\Criteria;
use App\Repository\Search\Search;

class MessageRepository extends Repository
{
    public function findAll(int $threadId, int $pageSize, int $page)
    {
        return $this->_findAll(Message::class, new Search([
            new Criteria('id_thread', $threadId)
        ]), $pageSize, $page);
    }

    public function create(Message $message)
    {
        return $this->_create($message);
    }

    public function exists(int $id): bool
    {
        return $this->_exists(Message::class, $id);
    }
}