<?php

namespace App\Controller;

use App\Controller\Dto\Base\Validator;
use App\Controller\Dto\Command\MessageCreate;
use App\Controller\Dto\Query\ThreadMessagesQuery;
use App\Controller\Dto\View\MessageView;
use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use App\Repository\UserRepository;
use App\Service\Session;
use DateTime;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MessageController
{
    private Session $session;
    private UserRepository $userRepository;
    private ThreadRepository $threadRepository;
    private MessageRepository $messageRepository;
    
    public function __construct()
    {
        $this->session = new Session;
        $this->userRepository = new UserRepository;
        $this->threadRepository = new ThreadRepository;
        $this->messageRepository = new MessageRepository;
    }

    public function list(ThreadMessagesQuery $query): JsonResponse
    {
        if (false === $this->session->isLogged()) {
            throw new BadRequestException('You must be logged.');
        }

        $errors = Validator::validate($query);
        if (0 !== count($errors)) {
            throw new BadRequestException(array_shift($errors));
        }

        $currentUsername = $this->session->getUser();
        $currentUser = $this->userRepository->findByUsername($currentUsername);

        if (!$thread = $this->threadRepository->get($query->id())) {
            throw new BadRequestException('This thread does not exist.');
        }

        if (!$thread->hasUser($currentUser)) {
            throw new BadRequestException('You are not allowed to view this thread.');
        }

        $messages = array_map(
            fn ($message) => new MessageView($message),
            $this->messageRepository->findAll(
                $query->id(),
                $query->pageSize(),
                $query->page(),
            )
        );
        return new JsonResponse($messages, 200);
    }

    public function create(MessageCreate $dto): JsonResponse
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

        if (!$thread = $this->threadRepository->get($dto->thread())) {
            throw new BadRequestException('This thread does not exist.');
        }

        if (!$thread->hasUser($currentUser)) {
            throw new BadRequestException('You are not allowed to modify this thread.');
        }

        $message = new Message;
        $message->user = $currentUser;
        $message->thread = $thread;
        $message->content = $dto->content();
        $message->sentAt = $dto->sentAt();

        $success = $this->messageRepository->create($message);
        $code = $success ? Response::HTTP_CREATED : Response::HTTP_INTERNAL_SERVER_ERROR;

        return new JsonResponse(['id' => $message->id], $code);
    }
}