<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Session;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThreadController
{
    private Session $session;
    private UserRepository $userRepository;
    
    public function __construct()
    {
        $this->session = new Session;
        $this->userRepository = new UserRepository;
    }
    
    public function create(Request $request)
    {
        $usersIds = $request->get('users');

        foreach ($usersIds as $userId) {
            $exists = $this->userRepository->exists(User::class, $userId);
            if (!$exists) {
                throw new BadRequestException("User #$userId does not exist");
            }
            
            
        }

        return new Response("You requested user $username !");
    }
}