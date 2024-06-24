<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    public $timestamps = false;
    protected $table = 'products';
    protected $fillable = [
        'id', 'shop_id', 'name', 'description', 'count', 'price', 'discount_price', 'tags', 'reminder', 'created_at'
    ];
}
