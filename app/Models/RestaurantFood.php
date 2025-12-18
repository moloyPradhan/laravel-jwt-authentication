<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;


class RestaurantFood extends Model
{
    use HasFactory, HasUid;

    protected $table = 'restaurant_foods';

    protected $fillable = [
        'uid',
        'restaurant_uid',
        'name',
        'slug',
        'code',
        'description',
        'price',
        'discount_price',
        'currency',
        'is_veg',
        'is_available',
        'preparation_time',
        'tags',
        'status',
    ];

    protected $hidden = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_uid', 'uid');
    }

    public function images()
    {
        return $this->hasMany(RestaurantFoodImage::class, 'food_uid', 'uid');
    }

    public function menus()
    {
        return $this->belongsToMany(RestaurantMenus::class, 'menu_food', 'food_id', 'menu_id', 'uid', 'uid')
            ->using(\App\Models\MenuFoodPivot::class)
            ->withTimestamps();
    }

    public function cartItems()
    {
        return $this->hasMany(
            Cart::class,
            'food_uid', // FK on cart_items table
            'uid'       // PK on restaurant_foods table
        );
    }
}
