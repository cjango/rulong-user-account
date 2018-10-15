<?php

/**
 * 用户关系扩展配置
 */

return [

    /**
     * 用户模型
     */
    'user_model'   => \App\User::class,

    /**
     * 账户类型
     */
    'account_type' => [
        'cash'  => '现金账户',
        'score' => '积分账户',
        'act_a' => '预留账户',
        'act_b' => '预留账户',
        'act_c' => '预留账户',
        'act_d' => '预留账户',
    ],
];
