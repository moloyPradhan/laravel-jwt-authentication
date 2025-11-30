<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;
use App\Traits\HasUid;

class MenuFoodPivot extends Pivot
{
    use HasUid;

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
}
