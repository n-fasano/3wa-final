<?php

namespace App\Controller;

use App\Controller\Dto\Base\Validator;
use App\Controller\Dto\UserCreate;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Password;
use App\Service\Session;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
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

    public function login(Request $request): Response
    {
        if (null === $username = $request->get('') ||
            null === $password = $request->get('password')
        ) {
            throw new BadRequestException('You must specify a username and a password');
        }

        $user = $this->userRepository->findByUsername($username);
        if (null === $user) {
            throw new BadRequestException('Unknown user or wrong password');
        }

        if (!(new Password)->verify($password, $user->getPassword())) {
            throw new BadRequestException('Unknown user or wrong password');
        }

        $this->session->login($user);

        return new Response();
    }

    public function logout(): Response
    {
        $this->session->logout();

        return new Response();
    }

    public function register_form(): Response
    {
        return new Response('
            <form action="/register" method="POST">
                <input type="text" name="username" placeholder="Enter your username">
                <input type="password" name="password" placeholder="Enter your password">
                <button>Register</button>
            </form>
        ');
    }

    public function register(UserCreate $dto): Response
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

        $user->setUsername($dto->username());
        $user->setPassword((new Password)->hash($dto->password()));

        $this->userRepository->create($user);
        $this->session->login($user);

        return new Response(null, Response::HTTP_CREATED);
    }
}