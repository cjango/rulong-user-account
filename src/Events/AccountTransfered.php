<?php

namespace RuLong\UserAccount\Events;

use Illuminate\Queue\SerializesModels;

class AccountTransfered
{

    use SerializesModels;

    public $fromUser;

    public $toUser;

    public $fromLog;

    public $toLog;

    public function __construct($fromUser, $toUser, $fromLog, $toLog)
    {
        $this->fromUser = $fromUser;
        $this->toUser   = $toUser;
        $this->fromLog  = $fromLog;
        $this->toLog    = $toLog;
    }

}
