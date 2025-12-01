<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSession extends Model
{

    protected $fillable = [
        'lms_session_id',
        'title',
        'mode',
        'lat',
        'lng',
        'geo_radius_m',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function isActive(): bool
    {
        return now()->between(
            $this->starts_at->copy()->subMinutes(15),
            $this->ends_at->copy()->addMinutes(30)
        );
    }
}
