<?php

namespace RuLong\UserAccount\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccountRule extends Model
{
    protected $guarded = [];

    public function logs()
    {
        return $this->hasMany(UserAccountLog::class, 'rule_id');
    }
}
