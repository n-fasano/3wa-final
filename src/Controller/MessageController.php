<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class UserController
{
    public function create(string $username)
    {
        return new Response("You requested user $username !");
    }

    public function chat(string $username)
    {
        return new Response("You requested user $username !");
    }
}