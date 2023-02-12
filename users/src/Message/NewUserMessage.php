<?php

namespace App\Message;

use App\Entity\User;

class NewUserMessage
{
    public function __construct(protected User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
