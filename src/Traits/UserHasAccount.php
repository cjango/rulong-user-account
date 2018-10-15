<?php

namespace RuLong\UserAccount\Traits;

use RuLong\UserAccount\Models\UserAccount;

trait UserHasAccount
{

    /**
     * 用户账户
     * @Author:<C.Jason>
     * @Date:2018-10-15T14:56:07+0800
     * @return [type] [description]
     */
    public function account()
    {
        return $this->hasOne(UserAccount::class);
    }
}
