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

    public function login(User $user): bool
    {
        $_SESSION['logged'] = true;
        $_SESSION['username'] = $user->username;

        return true;
    }

    public function logout(): bool
    {
        return session_destroy();
    }
}