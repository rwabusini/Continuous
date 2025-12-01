<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\TrainingSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TrainingSessionSeeder extends Seeder
{
    public function run(): void
    {
        TrainingSession::factory()->create([
            'lms_session_id' => (string) Str::uuid(),
            'title' => 'Weekly Lab',
            'mode' => 'hybrid',
            'location' => ['lat' => 25.2048, 'lng' => 55.2708],
            'geo_radius_m' => 150,
            'starts_at' => now()->addHour(),
            'ends_at' => now()->addHours(3),
        ]);
    }
}
