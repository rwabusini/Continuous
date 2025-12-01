<?php

declare(strict_types=1);

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Arr;

class PointCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?array
    {
        if (!$value) {
            return null;
        }

        [$lat, $lng] = array_map('floatval', explode(',', trim(str_replace(['POINT(', ')'], '', $value))));

        return ['lat' => $lat, 'lng' => $lng];
    }

    public function set($model, string $key, $value, array $attributes): ?string
    {
        if (!$value) {
            return null;
        }

        $lat = Arr::get($value, 'lat');
        $lng = Arr::get($value, 'lng');

        return sprintf('POINT(%F %F)', $lat, $lng);
    }
}
