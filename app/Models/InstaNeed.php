<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstaNeed extends Model
{
    public $timestamps = false;
    protected $table = 'insta_needs';
    protected $fillable = [
        'id', 'user_id', 'message_id', 'start_time', 'expire_time',
    ];
    protected $casts = [

    ];
}
