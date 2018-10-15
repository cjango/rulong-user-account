<?php

namespace RuLong\UserAccount\Traits;

use Account;
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

    /**
     * 执行账户规则
     * @Author:<C.Jason>
     * @Date:2018-10-15T16:40:50+0800
     * @param string|integer $rule 规则名称|规则ID
     * @param integer $variable 增减加变量
     * @param boolean $freezing 是否冻结
     * @param array $source 数据源信息
     */
    public function rule($rule, $variable = 0, $freezing = true, $source = [])
    {
        return Account::executeRule($this, $rule, $variable, $freezing, $source);
    }
}
