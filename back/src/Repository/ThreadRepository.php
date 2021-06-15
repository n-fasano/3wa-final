<?php

namespace App\Repository;

use App\Entity\Entity;
use App\Entity\Message;
use App\Entity\Thread;
use App\Entity\User;
use App\Mysql\Connection;
use App\Mysql\Query\Select;
use App\Mysql\Query\Where;
use App\Repository\Search\Criteria;
use App\Repository\Search\Search;

class ThreadRepository extends Repository
{
    public function get(int $id): ?Thread
    {
        $thread = $this->_get(Thread::class, $id);

        // $select = new Select(
        //     'thread_user',
        //     new Where(new Search(
        //         [new Criteria('id_thread', $thread->id)]
        //     )),
        //     ['id_user']
        // );

        // $usersData = Connection::query($select);
        // foreach ($usersData as $userData) {
        //     $thread->users[] = $this->_get(User::class, $userData['id_user']);
        // }

        // $thread->messages = $this->_findAll(Message::class, new Search(
        //     [new Criteria('id_thread', $thread->id)]
        // ));

        return $thread;
    }

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
            $thread = $this->_get(Thread::class, $threadData['id_thread']);

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
        if (false === $this->_create($thread)) {
            return false;
        }

        foreach ($thread->users as $user) {
            $table = 'thread_user';

            $sqlFields = 'id_thread, id_user';
            $sqlValues = '?, ?';
            $sqlParameters = [$thread->id, $user->id];
            
            $sql = "INSERT INTO $table ($sqlFields) VALUES ($sqlValues);";
            if (false === Connection::command($sql, $sqlParameters)) {
                return false;
            }
        }
        
        return true;
    }

    public function exists(int $id): bool
    {
        return $this->_exists(Thread::class, $id);
    }
}