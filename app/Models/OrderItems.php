<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'uid',
        'order_uid',
        'food_uid',
        'quantity',
        'price',
        'total',
    ];

    protected $hidden = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
