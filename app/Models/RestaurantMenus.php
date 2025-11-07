<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUid;


class RestaurantMenus extends Model
{
    use HasFactory, HasUid, SoftDeletes;

    protected $table = 'restaurant_menus';

    protected $fillable = [
        'uid',
        'restaurant_uid',
        'name',
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
}
