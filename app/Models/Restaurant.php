<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;

class Restaurant extends Model
{
    use HasFactory, HasUid;

    protected $fillable = [
        'uid',
        'user_uid',
        'name',
        'email',
        'phone',
        'description',
        'status',
    ];

    protected $hidden = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }


    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable', 'addressable_type', 'addressable_id', 'uid');
    }

    public function documents()
    {
        return $this->hasMany(RestaurantDocument::class, 'restaurant_uid', 'uid');
    }

    public function images()
    {
        return $this->hasMany(RestaurantImages::class, 'restaurant_uid', 'uid');
    }
}
