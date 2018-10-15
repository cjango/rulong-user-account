<?php

namespace RuLong\UserAccount;

use Carbon\Carbon;
use RuLong\UserAccount\Exceptions\UserRuleException;
use RuLong\UserAccount\Models\UserAccountRule;

class Account
{

    /**
     * @Author:<C.Jason>
     * @Date:2018-05-30
     */
    /**
     * 判断是否可执行，并且执行规则
     * @Author:<C.Jason>
     * @Date:2018-10-15T16:52:44+0800
     * @param App\User       $user [description]
     * @param string|integer $rule [description]
     * @param integer        $variable [description]
     * @param boolean        $freezing [description]
     * @param array          $source [description]
     */
    public function executeRule($user, $rule, $variable = 0, $freezing = false, $source = [])
    {
        if (is_numeric($rule)) {
            $rule = UserAccountRule::find($rule);
        } else {
            $rule = UserAccountRule::whereName($rule)->first();
        }
        if (empty($rule)) {
            throw new UserRuleException('规则不存在');
        }

        if ($rule->trigger == 0) {
            return $this->accountExecte($user, $rule, $variable, $freezing, $source);
        } elseif ($rule->trigger < 0 && !$user->accountLogs()->whereRuleId($rule->id)->first()) {
            return $this->accountExecte($user, $rule, $variable, $freezing, $source);
        } elseif ($user->accountLogs()->whereRuleId($rule->id)->whereDate('created_at', Carbon::today())->count() < $rule->trigger) {
            return $this->accountExecte($user, $rule, $variable, $freezing, $source);
        }

        return false;
    }

    /**
     * 账户增减的实际操作
     * @Author:<C.Jason>
     * @Date:2018-05-30
     * @param User $user [description]
     * @param AccountRule $rule [description]
     * @return [type] [description]
     */
    private function accountExecte(User $user, UserAccountRule $rule, $variable, $freezing, $source)
    {
        try {
            if ($variable != 0) {
                $rule->variable = $variable;
            }
            if (($rule->variable < 0) && ($rule->variable + $user->account()->value($rule->type) < 0)) {
                throw new UserRuleException('余额不足');
            }
            DB::transaction(function () use ($user, $rule, $freezing, $source) {
                if ($freezing === 0) {
                    $user->account()->increment($rule->type, $rule->variable);
                }
                $user->accountLogs()->create([
                    'rule_id'  => $rule->id,
                    'type'     => $rule->type,
                    'variable' => $rule->variable,
                    'freezing' => $freezing,
                    'balance'  => $user->account()->value($rule->type),
                    'source'   => $source,
                ]);
            });
            return true;
        } catch (\Exception $e) {
            throw new UserRuleException($e->getMessage());
        }
    }

    public function thaw(AccountLog $log)
    {
        try {
            if ($log->freezing == 1) {
                DB::transaction(function () use ($log) {
                    $log->user->account()->increment($log->type, $log->variable);
                    $log->freezing = 0;
                    $log->save();
                });
            } else {
                return '账目已经解冻';
            }
        } catch (\Exception $e) {
            return false;
        }
    }

}
