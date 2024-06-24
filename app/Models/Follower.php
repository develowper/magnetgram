<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

// channels and groups in active countdown state
class Follower extends Model
{
    public $timestamps = false;
    protected $table = 'followers';
    protected $fillable = [
        'telegram_id', 'chat_id', 'added_by', 'ref_score', 'follow_score', 'created_at'
    ];
}
