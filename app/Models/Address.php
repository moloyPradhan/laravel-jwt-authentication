<?php

namespace App\Models;

use App\Traits\HasUid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\UidHelper;


class Address extends Model
{
    use HasFactory, SoftDeletes, HasUid;

    protected $fillable = [
        'uid',
        'addressable_type',
        'addressable_id',
        'label',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'latitude',
        'longitude',
        'is_default',
        'is_active',
    ];

    protected $hidden = [
        'id',
        'addressable_id',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
        'latitude'   => 'decimal:8',
        'longitude'  => 'decimal:8',
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function getRestaurantUidAttribute()
    {
        if ($this->addressable_type === \App\Models\Restaurant::class && $this->addressable) {
            return $this->addressable->uid;
        }
        return null;
    }
}
