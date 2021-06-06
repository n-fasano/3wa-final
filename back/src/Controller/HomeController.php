<?php

namespace App\Controller;

use App\Service\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    private Session $session;
    
    public function __construct()
    {
        $this->session = new Session;
    }
    
    public function welcome()
    {
        // if (false === $this->session->isLogged()) {
        //     return new RedirectResponse('/login');
        // }

        return new Response('Welcome to the API !');
    }
}