<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Need extends Model
{


    public $timestamps = false;
    protected $table = 'needs';
    protected $fillable = [
        'id', 'user_id', 'description', 'group_id', 'message_id', 'start_time', 'expire_time',
    ];
    protected $casts = [

    ];
}
