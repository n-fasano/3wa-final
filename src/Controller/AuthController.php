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

    public function login(Request $request)
    {
        if (null === $username = $request->get('') ||
            null === $password = $request->get('password')
        ) {
            throw new BadRequestException('You must specify a username and a password');
        }

        $user = (new UserRepository)->findByUsername($username);
        if (null === $user) {
            throw new BadRequestException('Unknown user or wrong password');
        }

        if (!(new Password)->verify($password, $user->getPassword())) {
            throw new BadRequestException('Unknown user or wrong password');
        }


    }

    public function logout(Request $request)
    {
        $this->session->logout();
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

    public function register(UserCreate $dto)
    {
        $errors = Validator::validate($dto);
        dd($errors);

        if (0 !== count($errors)) {
            return new Response(null, Response::HTTP_BAD_REQUEST);
        }

        $user = new User;

        $user->setUsername($dto->username());
        $user->setPassword((new Password)->hash($dto->password()));

        $this->userRepository->create($user);
    }
}