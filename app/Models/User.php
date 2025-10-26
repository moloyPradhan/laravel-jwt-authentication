<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Helpers\UidHelper;
use Illuminate\Database\Eloquent\Relations\MorphMany;


class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'uid',
        'name',
        'email',
        'password',
        'status',
        'verification_code',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->uid)) {
                $user->uid = UidHelper::generate(8);
            }
        });
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtolower($value),
            get: fn($value) => ucwords($value)
        );
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn($value) => strtolower($value)
        );
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
        // return $this->uid; // <-- store uid instead of numeric id

    }

    public function getJWTCustomClaims()
    {
        return [
            'uid' => $this->uid,
            'name' => $this->name,
        ];
    }

    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable', 'addressable_type', 'addressable_id', 'uid');
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'user_uid', 'uid');
    }
}
