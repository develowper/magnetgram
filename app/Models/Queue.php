<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

// channels and groups in active countdown state
class Queue extends Model
{

    public $timestamps = false;
    protected $table = 'divar_queue';
    protected $fillable = [
        'user_id', 'chat_id', 'group_id', 'image', 'chat_type', 'chat_username', 'chat_title', 'chat_description', 'chat_main_color', 'show_time', 'is_vip'
    ];
}
