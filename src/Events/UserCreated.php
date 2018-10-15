<?php

namespace RuLong\UserAccount\Events;

class UserCreated
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
        $user->account()->create();
    }

}
