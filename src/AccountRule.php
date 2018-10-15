<?php

namespace RuLong\UserAccount;

use RuLong\UserAccount\Models\UserAccountRule;

class AccountRule
{

    protected $rule;

    public function __construct(UserAccountRule $rule)
    {
        $this->rule = $rule;
    }

    public function store()
    {
        UserAccountRule::create();
    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}
