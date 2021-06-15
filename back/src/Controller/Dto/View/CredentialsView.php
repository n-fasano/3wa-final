<?php

namespace App\Controller\Dto\View;

use App\Entity\User;

class CredentialsView extends UserView
{
    public function __construct(User $user, public bool $logged)
    {
        parent::__construct($user);
    }
}