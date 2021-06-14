<?php

namespace App\Controller;

use App\Controller\Dto\ThreadView;
use App\Entity\Thread;
use App\Entity\User;
use App\Repository\Search\Criteria;
use App\Repository\Search\Search;
use App\Repository\ThreadRepository;
use App\Repository\UserRepository;
use App\Service\Session;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThreadController
{
    private Session $session;
    private UserRepository $userRepository;
    private ThreadRepository $threadRepository;
    
    public function __construct()
    {
        $this->session = new Session;
        $this->userRepository = new UserRepository;
        $this->threadRepository = new ThreadRepository;
    }
    
    public function list(): JsonResponse
    {
        if (false === $this->session->isLogged()) {
            throw new BadRequestException('You must be logged.');
        }

        $username = $this->session->getUser();
        $user = $this->userRepository->findByUsername($username);

        $threads = $this->threadRepository->findByUser($user);

        $threads = [];
        $threadsData = [
            ['Nicolas', 'Amandine', 'Simon', 'Alanah'],
            ['Nicolas', 'Amandine'],
            ['Nicolas', 'Simon']
        ];
        foreach ($threadsData as $i => $users) {
            $thread = new Thread;
            $thread->id = $i + 1;
            foreach ($users as $username) {
                $_user = new User;
                $_user->username = $username;
                $thread->users[] = $_user;
            }
            $threads[] = $thread;
        }

        $threads = array_map(fn ($thread) => new ThreadView($thread), $threads);
        return new JsonResponse($threads, 200);
    }
    
    public function show(Request $request)
    {
        $id = $request->get('id');

        return new Response("You requested thread #$id !");
    }
    
    public function create(Request $request)
    {
        return new Response("You requested to create a thread !");
    }
}