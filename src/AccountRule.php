<?php

namespace RuLong\UserAccount;

use Illuminate\Validation\Rule;
use RuLong\UserAccount\Exceptions\AccountRuleException;
use RuLong\UserAccount\Models\UserAccountRule;
use Validator;

class AccountRule
{

    protected $rule;

    public function __construct(UserAccountRule $rule)
    {
        $this->rule = $rule;
    }

    /**
     * 新增规则
     * @Author:<C.Jason>
     * @Date:2018-10-16T12:59:48+0800
     * @param array $data 规则数组
     * @return AccountRuleException|boolean
     */
    public function store($data)
    {
        $validator = Validator::make($data, [
            'title'      => 'required|between:2,50',
            'name'       => 'required|alpha_dash|between:2,50|unique:user_account_rules',
            'type'       => ['required', Rule::in(array_keys(config('user_account.account_type')))],
            'variable'   => 'required|numeric',
            'trigger'    => 'required|integer',
            'deductions' => ['required', 'integer', Rule::in([0, 1])],
            'remark'     => 'nullable|max:255',
        ], [
            'title.required'      => '规则标题 必须填写',
            'title.between'       => '规则标题 长度应在:min-:max之间',
            'name.required'       => '规则名称 必须填写',
            'name.alpha_dash'     => '规则名称 只能是字母、数字、( - ) 或 ( _ ) 组成',
            'name.between'        => '规则名称 长度应在:min-:max之间',
            'name.unique'         => '规则名称 已经存在',
            'type.required'       => '账户类型 必须填写',
            'type.in'             => '账户类型 不合法',
            'type.required'       => '账户类型 必须填写',
            'variable.required'   => '增减变量 必须填写',
            'variable.numeric'    => '增减变量 只能是数字',
            'trigger.required'    => '账户类型 必须填写',
            'trigger.integer'     => '账户类型 只能是整数',
            'deductions.required' => '是否直达 必须填写',
            'deductions.integer'  => '是否直达 只能是整数',
            'deductions.in'       => '是否直达 只能是 0 或 1',
            'remark.max'          => '规则备注 最大长度不能超过:max',
        ]);

        if ($validator->fails()) {
            throw new AccountRuleException($validator->errors()->first());
        }

        return UserAccountRule::create($data);
    }

    /**
     * 更新规则
     * @Author:<C.Jason>
     * @Date:2018-10-16T13:00:15+0800
     * @param UserAccountRule $rule 要编辑的规则
     * @param array           $data 规则数组
     * @return AccountRuleException|boolean
     */
    public function update(UserAccountRule $rule, $data)
    {
        $validator = Validator::make($data, [
            'title'      => 'required|between:2,50',
            'name'       => ['required', 'alpha_dash', 'between:2,50', Rule::unique('user_account_rules')->ignore($rule->id)],
            'type'       => ['required', Rule::in(array_keys(config('user_account.account_type')))],
            'variable'   => 'required|numeric',
            'trigger'    => 'required|integer',
            'deductions' => ['required', 'integer', Rule::in([0, 1])],
            'remark'     => 'nullable|max:255',
        ], [
            'title.required'      => '规则标题 必须填写',
            'title.between'       => '规则标题 长度应在:min-:max之间',
            'name.required'       => '规则名称 必须填写',
            'name.alpha_dash'     => '规则名称 只能是字母、数字、( - ) 或 ( _ ) 组成',
            'name.between'        => '规则名称 长度应在:min-:max之间',
            'name.unique'         => '规则名称 已经存在',
            'type.required'       => '账户类型 必须填写',
            'type.in'             => '账户类型 不合法',
            'type.required'       => '账户类型 必须填写',
            'variable.required'   => '增减变量 必须填写',
            'variable.numeric'    => '增减变量 只能是数字',
            'trigger.required'    => '账户类型 必须填写',
            'trigger.integer'     => '账户类型 只能是整数',
            'deductions.required' => '是否直达 必须填写',
            'deductions.integer'  => '是否直达 只能是整数',
            'deductions.in'       => '是否直达 只能是 0 或 1',
            'remark.max'          => '规则备注 最大长度不能超过:max',
        ]);

        if ($validator->fails()) {
            throw new AccountRuleException($validator->errors()->first());
        }

        return $rule->update($data);
    }

    /**
     * 删除规则
     * @Author:<C.Jason>
     * @Date:2018-10-16T13:00:43+0800
     * @param integer $id 规则ID
     * @return integer
     */
    public function destroy($id)
    {
        return UserAccountRule::where('id', $id)->delete();
    }
}
