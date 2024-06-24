<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tab extends Model
{
    public $timestamps = false;
    protected $table = 'tabs';

    protected $fillable = [
        'user_id', 'chat_id', 'chat_title', 'group', 'chat_type', 'chat_username', 'members', 'message_id', 'processed', 'created_at'
    ];
    protected $casts = [
        // 'chat_id' => 'string',
//        'expire_time' => 'timestamp',
//        'start_time' => 'timestamp',
    ];
}
