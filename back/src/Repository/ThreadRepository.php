<?php

namespace App\Repository;

use App\Entity\Entity;
use App\Entity\Thread;
use App\Entity\User;
use App\Mysql\Connection;
use App\Mysql\Query\Select;
use App\Mysql\Query\Where;
use App\Repository\Search\Criteria;
use App\Repository\Search\Search;

class ThreadRepository extends Repository
{
    /** @return array<Thread> */
    public function findByUser(User $user): array
    {
        $threads = [];

        $select = new Select(
            'thread_user',
            new Where(new Search(
                [new Criteria('id_user', $user->id)]
            )),
            ['id_thread']
        );
        $threadsData = Connection::query($select);
        
        foreach ($threadsData as $threadData) {
            $thread = new Thread;

            $select = new Select(
                'thread_user',
                new Where(new Search(
                    [new Criteria('id_thread', $threadData['id_thread'])]
                )),
                ['id_user']
            );
            $usersData = Connection::query($select);

            foreach ($usersData as $userData) {
                $thread->users[] = $this->_get(User::class, $userData['id_user']);
            }

            $threads[] = $thread;
        }

        return $threads;
    }

    public function create(Entity $thread): bool 
    {

        
        return false;
    }
}