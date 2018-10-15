<?php

namespace RuLong\UserAccount\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccountLog extends Model
{
    protected $guarded = [];

    public function account()
    {
        return $this->belongsTo(Account::class, 'user_id', 'user_id');
    }

    public function rule()
    {
        return $this->belongsTo(UserAccountRule::class);
    }
}
