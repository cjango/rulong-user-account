<?php

namespace RuLong\UserAccount\Facades;

use Illuminate\Support\Facades\Facade;

class AccountRule extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RuLong\UserAccount\AccountRule::class;
    }
}
