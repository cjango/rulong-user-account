<?php

namespace RuLong\UserAccount\Events;

use Illuminate\Queue\SerializesModels;

class AccountLogFrozened
{

    use SerializesModels;

    public $log;

    public function __construct($log)
    {
        $user->log = $log;
    }

}
