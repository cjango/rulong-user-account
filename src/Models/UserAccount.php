<?php

namespace RuLong\UserAccount\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    protected $guarded = [];

    public function logs()
    {
        return $this->hasMany(UserAccountLog::class, 'user_id', 'user_id');
    }
}
