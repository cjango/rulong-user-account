<?php

namespace RuLong\UserAccount\Facades;

use Illuminate\Support\Facades\Facade;

class Account extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \RuLong\UserAccount\Account::class;
    }
}
