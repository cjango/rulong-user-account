<?php

namespace RuLong\UserAccount;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuLong\UserAccount\Exceptions\ExecuteRuleException;
use RuLong\UserAccount\Models\UserAccountLog;
use RuLong\UserAccount\Models\UserAccountRule;

class Account
{

    /**
     * 判断是否可执行，并且执行规则
     * @Author:<C.Jason>
     * @Date:2018-10-15T16:52:44+0800
     * @param User           $user     用户模型
     * @param string|integer $rule     账户规则
     * @param numeric        $variable 自定义增减量
     * @param boolean        $frozen   是否冻结
     * @param array          $source   溯源信息
     * @return
     */
    public function executeRule($user, $rule, $variable = 0, $frozen = false, $source = [])
    {
        $this->isUserModel($user);

        if (is_numeric($rule)) {
            $rule = UserAccountRule::find($rule);
        } else {
            $rule = UserAccountRule::where('name', $rule)->first();
        }

        if (!$rule) {
            throw new ExecuteRuleException('账户规则不存在');
        }

        if ($rule->trigger == 0) {
            // 不限制执行的
            return $this->accountExecte($user, $rule, $variable, $frozen, $source);
        } elseif ($rule->trigger > $user->account->logs()->where('rule_id', $rule->id)->whereDate('created_at', Carbon::today())->count()) {
            // 每日执行 trigger 次
            return $this->accountExecte($user, $rule, $variable, $frozen, $source);
        } elseif ($rule->trigger < 0 && !$user->account->logs()->where('rule_id', $rule->id)->first()) {
            // 终身只能执行一次
            return $this->accountExecte($user, $rule, $variable, $frozen, $source);
        }

        throw new ExecuteRuleException('达到最大可执行次数');
    }

    /**
     * 账户记录解冻
     * @Author:<C.Jason>
     * @Date:2018-10-16T09:16:13+0800
     * @param UserAccountLog $log 账户记录
     * @return ExecuteRuleException|boolean
     */
    public function thaw(UserAccountLog $log)
    {
        try {
            if ($log->frozen == 1) {
                DB::transaction(function () use ($log) {
                    $log->account->increment($log->type, $log->variable);
                    $log->frozen  = 0;
                    $log->balance = $log->account->{$log->type};
                    $log->save();
                });

                return true;
            } else {
                throw new ExecuteRuleException('账目已解冻');
            }
        } catch (\Exception $e) {
            throw new ExecuteRuleException($e->getMessage());
        }
    }

    /**
     * 冻结一条账户记录
     * @Author:<C.Jason>
     * @Date:2018-10-16T09:40:23+0800
     * @param UserAccountLog $log 账户记录
     * @return ExecuteRuleException|boolean
     */
    public function frozen(UserAccountLog $log)
    {
        try {
            if ($log->frozen == 0) {
                DB::transaction(function () use ($log) {
                    $log->account->decrement($log->type, $log->variable);
                    $log->frozen  = 1;
                    $log->balance = $log->account->{$log->type};
                    $log->save();
                });

                return true;
            } else {
                throw new ExecuteRuleException('账目已冻结');
            }
        } catch (\Exception $e) {
            throw new ExecuteRuleException($e->getMessage());
        }
    }

    /**
     * 转账给用户
     * @Author:<C.Jason>
     * @Date:2018-10-16T09:47:35+0800
     * @param User    $fromUser 发起转账用户
     * @param User    $toUser   目标用户
     * @param string  $type     账户类型
     * @param numeric $variable 自定义转账量
     * @return ExecuteRuleException|boolean
     */
    public function transfer($fromUser, $toUser, $type, $variable)
    {
        $this->isUserModel($fromUser);
        $this->isUserModel($toUser);
        $this->isLegalType($type);

        if ($variable <= 0) {
            throw new ExecuteRuleException('转账金额不能为负数');
        }

        if (($fromUser->account->{$type}) - $variable < 0) {
            throw new ExecuteRuleException('【 ' . config('user_account.account_type')[$type] . ' 】 余额不足');
        }

        DB::transaction(function () use ($fromUser, $toUser, $type, $variable) {
            $feature = Str::uuid();
            $fromUser->account->decrement($type, $variable);
            $fromUser->account->logs()->create([
                'rule_id'  => 0,
                'type'     => $type,
                'variable' => -$variable,
                'frozen'   => 0,
                'balance'  => $fromUser->account->{$type},
                'source'   => ['type' => 'transfer', 'fromUser' => $fromUser->id, 'toUser' => $toUser->id, 'feature' => $feature],
            ]);

            $toUser->account->increment($type, $variable);
            $toUser->account->logs()->create([
                'rule_id'  => 0,
                'type'     => $type,
                'variable' => $variable,
                'frozen'   => 0,
                'balance'  => $toUser->account->{$type},
                'source'   => ['type' => 'transfer', 'fromUser' => $fromUser->id, 'toUser' => $toUser->id, 'feature' => $feature],
            ]);
        });

        return true;
    }

    /**
     * 增加账户余额
     * @Author:<C.Jason>
     * @Date:2018-10-16T10:20:58+0800
     * @param User    $user 用户模型
     * @param string  $type 账户类型
     * @param numeric $variable 自定义增减量
     * @return ExecuteRuleException|boolean
     */
    public function increase($user, $type, $variable)
    {
        $this->isUserModel($user);
        $this->isLegalType($type);

        DB::transaction(function () use ($user, $type, $variable) {
            $user->account->increment($type, $variable);

            $user->account->logs()->create([
                'rule_id'  => 0,
                'type'     => $type,
                'variable' => $variable,
                'frozen'   => 0,
                'balance'  => $user->account->{$type},
                'source'   => ['type' => 'increase'],
            ]);
        });

        return true;
    }

    /**
     * 扣除账户金额
     * @Author:<C.Jason>
     * @Date:2018-10-16T09:49:36+0800
     * @param User    $user 用户模型
     * @param string  $type 账户类型
     * @param numeric $variable 自定义增减量
     * @return ExecuteRuleException|boolean
     */
    public function decrease($user, $type, $variable)
    {
        $this->isUserModel($user);
        $this->isLegalType($type);

        // 如果账户类型不可以为负数
        if (config('user_account.can_minus')[$type] === false && ($user->account->$type + $variable < 0)) {
            throw new ExecuteRuleException('【 ' . config('user_account.account_type')[$type] . ' 】 余额不足');
        }

        DB::transaction(function () use ($user, $type, $variable) {
            $user->account->decrement($type, $variable);

            $user->account->logs()->create([
                'rule_id'  => 0,
                'type'     => $type,
                'variable' => -$variable,
                'frozen'   => 0,
                'balance'  => $user->account->{$type},
                'source'   => ['type' => 'deduct'],
            ]);
        });

        return true;
    }

    /**
     * 判断模型是否是用户模型
     * @Author:<C.Jason>
     * @Date:2018-10-16T10:01:55+0800
     * @param object $model 被判断的模型
     * @return ExecuteRuleException|void
     */
    private function isUserModel($model)
    {
        $userModel = config('user_account.user_model');
        if (!($model instanceof $userModel)) {
            throw new ExecuteRuleException('不正确的用户模型');
        }
    }

    /**
     * 判断是否是合法的账户类型
     * @Author:<C.Jason>
     * @Date:2018-10-16T10:43:41+0800
     * @param string $type 账户类型
     * @return ExecuteRuleException|void
     */
    private function isLegalType($type)
    {
        $typeList = config('user_account.account_type');

        if (!in_array($type, array_keys($typeList))) {
            throw new ExecuteRuleException('不合法的账户类型');
        }
    }

    /**
     * 账户增减的实际操作
     * @Author:<C.Jason>
     * @Date:2018-05-30
     * @param User        $user     用户模型
     * @param AccountRule $rule     规则模型
     * @param numeric     $variable 自定义增减量
     * @param boolean     $frozen   是否冻结
     * @param array       $source   溯源信息
     * @return ExecuteRuleException|boolean
     */
    private function accountExecte($user, UserAccountRule $rule, $variable, $frozen, $source)
    {
        try {
            if ($variable != 0) {
                $rule->variable = $variable;
            }

            // 账户余额不允许为负数的时候判断余额是否充足
            if ((config('user_account.can_minus')[$rule->type] == false) && ($rule->variable < 0) && ($rule->variable + $user->account->{$rule->type} < 0)) {
                throw new ExecuteRuleException('【 ' . config('user_account.account_type')[$rule->type] . ' 】 余额不足');
            }

            DB::transaction(function () use ($user, $rule, $frozen, $source) {
                // 如果是扣款，立即执行，如果非冻结，也立即执行
                if (($rule->variable < 0 && config('user_account.deductions')) || $rule->deductions == 1 || $frozen === false) {
                    $user->account->increment($rule->type, $rule->variable);
                    $frozen = false;
                }

                // 写入记录
                $user->account->logs()->create([
                    'rule_id'  => $rule->id,
                    'type'     => $rule->type,
                    'variable' => $rule->variable,
                    'frozen'   => $frozen,
                    'balance'  => $user->account->{$rule->type},
                    'source'   => $source ?: null,
                ]);
            });

            return true;
        } catch (\Exception $e) {
            throw new ExecuteRuleException($e->getMessage());
        }
    }

}
