<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_session_id',
        'lms_user_id',
        'mode',
        'geo_confidence',
        'risk_score',
        'lat',
        'lng',
        'ip_hash',
        'checked_in_at',
        'checked_out_at',
        'challenge_passed_at',
        'last_beacon_at',
        'flags',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'challenge_passed_at' => 'datetime',
        'last_beacon_at' => 'datetime',
        'flags' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class, 'training_session_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(AttendanceEvent::class);
    }

    public function markBeacon(array $payload): void
    {
        $this->forceFill([
            'last_beacon_at' => now(),
            'risk_score' => min(100, $this->risk_score + ($payload['missed'] ? 10 : 0)),
        ])->save();

        $this->events()->create([
            'type' => 'beacon',
            'payload' => $payload,
        ]);
    }
}
