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
     * @return \RuLong\UserAccount\Models\UserAccount::class
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
     * @param boolean $frozen 是否冻结
     * @param array $source 数据源信息
     * @return AccountRuleException|boolean
     */
    public function rule($rule, $variable = 0, $frozen = true, $source = [])
    {
        return Account::executeRule($this, $rule, $variable, $frozen, $source);
    }

    /**
     * 转账
     * @Author:<C.Jason>
     * @Date:2018-10-16T10:42:25+0800
     * @param User    $toUser   目标用户
     * @param string  $type     账户类型
     * @param numeric $variable 自定义转账量
     * @return AccountRuleException|boolean
     */
    public function transfer($toUser, $type, $variable)
    {
        return Account::transfer($this, $toUser, $type, $variable);
    }

    /**
     * 增加账户余额
     * @Author:<C.Jason>
     * @Date:2018-10-16T10:42:31+0800
     * @param string  $type 账户类型
     * @param numeric $variable 自定义增减量
     * @return AccountRuleException|boolean
     */
    public function increase($type, $variable)
    {
        return Account::increase($this, $type, $variable);
    }

    /**
     * 扣除账户金额
     * @Author:<C.Jason>
     * @Date:2018-10-16T10:42:37+0800
     * @param string  $type 账户类型
     * @param numeric $variable 自定义增减量
     * @return AccountRuleException|boolean
     */
    public function decrease($type, $variable)
    {
        return Account::decrease($this, $type, $variable);
    }

}
