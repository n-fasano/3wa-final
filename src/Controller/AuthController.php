<?php

namespace App\Controller;

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

    public function register_form()
    {
        return new Response(`
            <form action="/register" method="POST">
                <input type="text" name="username" placeholder="Enter your username">
                <input type="password" name="password" placeholder="Enter your password">
            </form>
        `);
    }

    public function register(string $username, string $password)
    {
        $user = new User;

        $user->setUsername($username);
        $user->setPassword((new Password)->hash($password));

        $this->userRepository->create($user);
    }
}