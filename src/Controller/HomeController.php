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
    
    public function home()
    {
        // if (false === $this->session->isLogged()) {
        //     return new RedirectResponse('/login');
        // }

        return new Response(file_get_contents(ROOT.'/public/index.html'));
    }
}