<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;

class MenuFood extends Model
{
    use HasFactory, HasUid;

    protected $table = 'menu_food';

    protected $fillable = [
        'uid',
        'food_id',
        'menu_id',
    ];

    protected $hidden = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];


    public function foods()
    {
        return $this->belongsToMany(RestaurantFood::class);
    }
}
