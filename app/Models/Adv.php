<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Adv extends Model
{

    protected $table = 'advs';
    protected $fillable = [
        'id', 'title', 'clicks', 'is_active', 'banner_link', 'click_link', 'created_at'
    ];
    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean',
        'created_at' => 'timestamp',

    ];
}
