<?php

namespace App\Controller;

use App\Controller\Dto\Base\Validator;
use App\Controller\Dto\Command\UserCreate;
use App\Controller\Dto\View\CredentialsView;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Password;
use App\Service\Session;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController
{
    private Session $session;
    private UserRepository $userRepository;
    
    public function __construct()
    {
        $this->session = new Session;
        $this->userRepository = new UserRepository;
    }

    public function credentials(): JsonResponse
    {
        if ($this->session->isLogged()) {
            $username = $this->session->getUser();
            $user = $this->userRepository->findByUsername($username);
        } else {
            $user = new User;
            $user->id = 0;
            $user->username = 'Anon';
        }

        $view = new CredentialsView($user, $this->session->isLogged());
        return new JsonResponse($view, Response::HTTP_OK);
    }

    public function login(Request $request): JsonResponse
    {
        if (null === ($username = $request->get('username')) ||
            null === ($password = $request->get('password'))
        ) {
            throw new BadRequestException('You must specify a username and a password');
        }

        $user = $this->userRepository->findByUsername($username);
        if (null === $user) {
            throw new BadRequestException('Unknown user or wrong password');
        }

        if (!(new Password)->verify($password, $user->password)) {
            throw new BadRequestException('Unknown user or wrong password');
        }

        $success = $this->session->login($user);
        $code = $success ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR;

        $view = new CredentialsView($user, $this->session->isLogged());
        return new JsonResponse($view, $code);
    }

    public function logout(): JsonResponse
    {
        $success = $this->session->logout();
        $code = $success ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR;

        return new JsonResponse([], $code);
    }

    public function register(UserCreate $dto): JsonResponse
    {
        $errors = Validator::validate($dto);
        if (0 !== count($errors)) {
            throw new BadRequestException(array_shift($errors));
        }

        $username = $dto->username();
        $user = $this->userRepository->findByUsername($username);
        if (null !== $user) {
            throw new BadRequestException("Username $username is already taken.");
        }

        $user = new User;

        $user->username = $dto->username();
        $user->password = (new Password)->hash($dto->password());

        $success = $this->userRepository->create($user);
        $code = $success ? Response::HTTP_CREATED : Response::HTTP_INTERNAL_SERVER_ERROR;

        return new JsonResponse(['id' => $user->id], $code);
    }
}