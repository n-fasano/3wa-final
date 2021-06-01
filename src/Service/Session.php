<?php

namespace App\Service;

use App\Entity\User;

session_start();

class Session
{
    public function isLogged(): bool
    {
        return $_SESSION['logged'] ?? false;
    }

    public function getUser()
    {
        return $_SESSION['username'] ?? null;
    }

    public function login(User $user)
    {
        $_SESSION['logged'] = true;
        $_SESSION['username'] = $user->getUsername();
    }

    public function logout()
    {
        session_destroy();
    }
}