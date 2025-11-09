<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'from_user',
        'message',
    ];

    protected $hidden = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
