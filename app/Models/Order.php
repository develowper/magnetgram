<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{


    public $timestamps = false;
    protected $fillable = [
        'id', 'chat_id', 'chat_username', 'user_id', 'budget', 'done_now', 'done_score', 'ref_score', 'type', 'active', 'created_at', 'expired_at',
    ];
}
