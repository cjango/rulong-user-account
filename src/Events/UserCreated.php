<?php

namespace RuLong\UserAccount\Events;

use Illuminate\Queue\SerializesModels;

class UserCreated
{

    use SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
        $user->account()->create();
    }

}
