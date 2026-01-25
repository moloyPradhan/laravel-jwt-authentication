<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'uid',
        'user_uid',
        'address_uid',
        'amount',
        'status',
    ];

    protected $hidden = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function order_items()
    {
        return $this->hasMany(OrderItems::class, 'order_uid', 'uid');
    }
}
