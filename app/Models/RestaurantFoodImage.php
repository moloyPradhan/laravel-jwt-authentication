<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;


class RestaurantFoodImage extends Model
{
    use HasFactory, HasUid;

    protected $table = 'restaurant_food_images';

    protected $fillable = [
        'uid',
        'food_uid',
        'image_url',
        'image_type',
        'sort_order',
        'is_primary',
    ];

    protected $hidden = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_uid', 'uid');
    }

    
}
