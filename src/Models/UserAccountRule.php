<?php

namespace RuLong\UserAccount\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAccountRule extends Model
{

    use SoftDeletes;

    protected $guarded = [];

    public function logs()
    {
        return $this->hasMany(UserAccountLog::class, 'rule_id');
    }
}
