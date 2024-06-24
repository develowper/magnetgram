<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{


    public $timestamps = false;
    protected $table = 'shops';
    protected $fillable = [
        'id', 'user_id', 'channel_address', 'page_address', 'site_address', 'name', 'description', 'group_id', 'timestamp', 'active', 'subscribe', 'created_at'
    ];
    protected $casts = [
        // 'chat_id' => 'string',
//        'expire_time' => 'timestamp',
//        'start_time' => 'timestamp',
    ];
}
