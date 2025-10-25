<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class UidHelper
{
    public static function generate(int $length = 8): string
    {
        return substr(str_replace('-', '', Str::uuid()), 0, $length);
    }
}
