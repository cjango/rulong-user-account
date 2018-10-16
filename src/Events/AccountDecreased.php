<?php

namespace RuLong\UserAccount\Events;

use Illuminate\Queue\SerializesModels;

class AccountDecreased
{

    use SerializesModels;

    public $account;

    public $log;

    public function __construct($account, $log)
    {
        $this->account = $account;
        $user->log     = $log;
    }

}
