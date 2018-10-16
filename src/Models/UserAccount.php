<?php

namespace RuLong\UserAccount\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    protected $guarded = [];

    protected $primaryKey = 'user_id';

    public function logs()
    {
        return $this->hasMany(UserAccountLog::class, 'user_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(config('user_account.user_model'));
    }
}
