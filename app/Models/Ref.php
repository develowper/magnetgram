<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ref extends Model
{
    public $timestamps = false;
    protected $table = 'refs';
    protected $fillable = [
        'new_telegram_id', 'invited_by'
    ];
}
