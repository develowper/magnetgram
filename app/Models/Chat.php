<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

// channels and groups in active countdown state
class Chat extends Model
{
    public $timestamps = false;
    protected $table = 'chats';
    protected $fillable = [
        'user_id', 'user_telegram_id', 'chat_id', 'group_id', 'chat_type', 'chat_username', 'image',
        'chat_main_color', 'chat_title', 'chat_description', 'active', 'auto_tag', 'auto_tab', 'auto_tab_day', 'auto_msg_day', 'auto_msg_night', 'auto_fun', 'tag'
    ];
    protected $casts = [
        'chat_id' => 'string',

        'user_id' => 'string',
        //'expire_time' => 'timestamp',

    ];
}
