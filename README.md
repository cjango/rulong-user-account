# 用户账户管理

## 1.安装
> composer require rulong/user-account

## 2.创建配置文件
> php artisan vendor:publish --provider="RuLong\UserAccount\ServiceProvider"

## 3.创建数据库
> php artisan migrate

## 4.初始化账户
> php artisan user:account

## 5.在系统中使用

### 1.Trait模式
~~~
use RuLong\UserAccount\Traits\UserHasAccount;

class User extends Authenticatable
{
    use UserHasAccount;

    public $guarded = [];
}
~~~
#### 可用方法

~~~
// 执行规则
$user->rule($rule, $variable = 0, $frozen = true, $source = []);
// 转账
$user->transfer($toUser, $type, $variable);
// 增余额
$user->increase($type, $variable);
// 减余额
$user->decrease($type, $variable);
~~~
### 2.Facade模式
~~~
// 执行规则
Account::executeRule($user, $rule, $variable = 0, $frozen = false, $source = []);
// 解冻
Account::thaw(UserAccountLog $log);
// 冻结
Account::frozen(UserAccountLog $log);
// 转账
Account::transfer($fromUser, $toUser, $type, $variable);
// 增余额
Account::increase($user, $type, $variable);
// 减余额
Account::decrease($user, $type, $variable);
~~~

## 6.规则管理
Facade模式
~~~
$data = [
	'title'      => $title, // string 规则名称
	'name'       => $name, // string 调用标记
	'type'       => $type, // 账户类型，参见配置文件
	'variable'   => $variable, // numeric 增减变量
	'trigger'    => $trigger, // 执行次数限制，小于0则终身一次，等于0不限制，大于0每日N次
	'deductions' => $deductions, // 0|1 直接处理，不冻结
	'remark'     => $remark, // nullable 备注信息
];
// 新增规则
AccountRule::store($data);
// 更新规则
AccountRule::update(UserAccountRule $rule, $data);
// 删除规则
AccountRule::destroy($id);
~~~

## 7.事件
~~~
// 扣除账户余额
AccountDecreased($account, $log);
// 增加账户余额
AccountIncreased($account, $log);
// 账户记录冻结
AccountLogFrozened($log);
// 账户记录解冻
AccountLogThawed($account, $log);
// 账户规则执行完毕
AccountRuleExecuted($account, $log);
// 账户转账完成
AccountTransfered($fromUser, $toUser, $fromLog, $toLog);
// 用户注册完成
UserCreated($user);
~~~