<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUid
{
    protected static function bootHasUid(): void
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getUidColumn()})) {
                $model->{$model->getUidColumn()} = $model->generateUid();
            }
        });
    }

    public function getUidColumn(): string
    {
        return property_exists($this, 'uidColumn') ? $this->uidColumn : 'uid';
    }

    public function generateUid(int $length = 8): string
    {
        $attempts = 0;
        $maxAttempts = 10;

        do {
            $uid = Str::random($length);
            $exists = static::where($this->getUidColumn(), $uid)->exists();
            $attempts++;

            if ($attempts >= $maxAttempts) {
                throw new \RuntimeException('Unable to generate unique UID');
            }
        } while ($exists);

        return $uid;
    }

    public function getRouteKeyName(): string
    {
        return $this->getUidColumn();
    }

    public function scopeByUid($query, string $uid)
    {
        return $query->where($this->getUidColumn(), $uid);
    }

    /**
     * Find model by UID
     */
    public static function findByUid(string $uid)
    {
        return static::where((new static)->getUidColumn(), $uid)->first();
    }

    /**
     * Find model by UID or fail
     */
    public static function findByUidOrFail(string $uid)
    {
        return static::where((new static)->getUidColumn(), $uid)->firstOrFail();
    }
}
