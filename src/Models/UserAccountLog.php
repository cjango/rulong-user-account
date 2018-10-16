<?php

namespace RuLong\UserAccount\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccountLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'source' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(UserAccount::class, 'user_id', 'user_id');
    }

    public function rule()
    {
        return $this->belongsTo(UserAccountRule::class)->withDefault();
    }
}
