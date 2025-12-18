<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;

class Cart extends Model
{
    use HasFactory, HasUid;

    protected $table = 'cart_items';

    protected $fillable = [
        'uid',
        'user_uid',
        'guest_uid',
        'restaurant_uid',
        'food_uid',
        'quantity',
    ];

    protected $hidden = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }

    public function food()
    {
        return $this->belongsTo(
            RestaurantFood::class,
            'food_uid', // FK on cart_items table
            'uid'       // PK on restaurant_foods table
        );
    }
}
