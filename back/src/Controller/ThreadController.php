<?php

namespace App\Controller;

use App\Controller\Dto\Base\Validator;
use App\Controller\Dto\Command\ThreadCreate;
use App\Controller\Dto\View\ThreadDetailsView;
use App\Controller\Dto\View\ThreadView;
use App\Entity\Message;
use App\Entity\Thread;
use App\Entity\User;
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

        $threads = array_map(
            fn ($thread) => new ThreadView($thread),
            $this->threadRepository->findByUser($user)
        );
        return new JsonResponse($threads, 200);
    }
    
    public function show(Request $request): JsonResponse
    {
        if (false === $this->session->isLogged()) {
            throw new BadRequestException('You must be logged.');
        }

        $currentUsername = $this->session->getUser();
        $currentUser = $this->userRepository->findByUsername($currentUsername);

        $id = (int) $request->get('id');
        if (!$thread = $this->threadRepository->get($id)) {
            throw new BadRequestException('This thread does not exist.');
        }

        if (!in_array($currentUser, [...$thread->users])) {
            throw new BadRequestException('You are not allowed to view this thread.');
        }

        $thread = new ThreadDetailsView($thread);
        return new JsonResponse($thread);
    }
    
    public function create(ThreadCreate $dto): JsonResponse
    {
        if (false === $this->session->isLogged()) {
            throw new BadRequestException('You must be logged.');
        }

        $errors = Validator::validate($dto);
        if (0 !== count($errors)) {
            throw new BadRequestException(array_shift($errors));
        }

        $currentUsername = $this->session->getUser();
        $currentUser = $this->userRepository->findByUsername($currentUsername);

        $thread = new Thread;
        $thread->users = array_map(
            fn($id) => $this->userRepository->get($id),
            $dto->users()
        );
        $thread->users[] = $currentUser;

        $userThreads = $this->threadRepository->findByUser($currentUser);
        foreach ($userThreads as $userThread) {
            $diff = array_udiff(
                (array) $thread->users,
                (array) $userThread->users,
                function (User $a, User $b) {
                    return $a->id === $b->id ? 0 : -1;
                }
            );
            
            if (0 === count($diff)) {
                throw new BadRequestException('A thread with these users already exists.');
            }
        }
        
        $success = $this->threadRepository->create($thread);
        $code = $success ? Response::HTTP_CREATED : Response::HTTP_INTERNAL_SERVER_ERROR;

        return new JsonResponse(['id' => $thread->id], $code);
    }
}