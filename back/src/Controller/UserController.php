<?php

namespace App\Controller;

use App\Controller\Dto\View\UserView;
use App\Repository\Search\Criteria;
use App\Repository\UserRepository;
use App\Service\Session;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController
{
    private Session $session;
    private UserRepository $userRepository;
    
    public function __construct()
    {
        $this->session = new Session;
        $this->userRepository = new UserRepository;
    }
    
    public function list(Request $request): JsonResponse
    {
        if (false === $this->session->isLogged()) {
            throw new BadRequestException('You must be logged.');
        }

        $criterias = [];
        if ($username = $request->get('username')) {
            $criterias[] = new Criteria(
                'username',
                $username,
                Criteria::TYPE_SIMILAR
            );
        }

        $users = array_map(
            fn ($user) => new UserView($user), 
            $this->userRepository->findAll($criterias)
        );

        return new JsonResponse($users, 200);
    }
}