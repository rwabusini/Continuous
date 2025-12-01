<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\TrainingSession;
use App\Services\QrTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function trainee_can_check_in_with_valid_qr_token(): void
    {
        $this->be($user = $this->mockLmsUser());
        $session = TrainingSession::factory()->create([
            'starts_at' => now()->subMinutes(5),
            'ends_at' => now()->addHour(),
        ]);

        $token = app(QrTokenService::class)->make($session, 'remote', $user->lms_id);

        $response = $this->postJson('/attendance/check-in', [
            'token' => $token,
            'lat' => 25.2,
            'lng' => 55.27,
            'geo_confidence' => 0.9,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('attendances', [
            'training_session_id' => $session->id,
            'lms_user_id' => $user->lms_id,
            'mode' => 'remote',
        ]);
    }

    protected function mockLmsUser()
    {
        return new class {
            public string $lms_id = 'user-123';
        };
    }
}
