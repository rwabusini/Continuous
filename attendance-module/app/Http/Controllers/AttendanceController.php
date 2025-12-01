<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\BeaconRequest;
use App\Http\Requests\ChallengeRequest;
use App\Http\Requests\CheckInRequest;
use App\Models\Attendance;
use App\Models\TrainingSession;
use App\Services\QrTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function checkIn(CheckInRequest $request, QrTokenService $tokens): JsonResponse
    {
        $token = $tokens->validate($request->input('token'));
        $session = TrainingSession::where('lms_session_id', $token->session)->firstOrFail();

        abort_unless($session->isActive(), 403, 'Session inactive');

        $attendance = Attendance::updateOrCreate(
            [
                'training_session_id' => $session->id,
                'lms_user_id' => Auth::user()->lms_id,
            ],
            [
                'mode' => $token->mode,
                'lat' => $this->toFloat($request->input('lat')),
                'lng' => $this->toFloat($request->input('lng')),
                'geo_confidence' => $this->toFloat($request->input('geo_confidence'), 0.0) ?? 0.0,
                'ip_hash' => hash('sha256', $request->ip()),
                'checked_in_at' => now(),
            ]
        );

        $attendance->events()->create([
            'type' => 'check_in',
            'payload' => $request->only('lat', 'lng', 'geo_confidence'),
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function submitChallenge(ChallengeRequest $request, TrainingSession $session): JsonResponse
    {
        $expected = Cache::get("session:{$session->id}:challenge");

        abort_if(!$expected || $expected !== strtoupper($request->keyword), 422, 'Invalid keyword');

        $attendance = Attendance::where([
            'training_session_id' => $session->id,
            'lms_user_id' => Auth::user()->lms_id,
        ])->firstOrFail();

        $attendance->forceFill([
            'challenge_passed_at' => now(),
            'risk_score' => max(0, $attendance->risk_score - 10),
        ])->save();

        $attendance->events()->create([
            'type' => 'challenge',
            'payload' => ['keyword' => $request->keyword],
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function beacon(BeaconRequest $request, TrainingSession $session): JsonResponse
    {
        $attendance = Attendance::where([
            'training_session_id' => $session->id,
            'lms_user_id' => Auth::user()->lms_id,
        ])->firstOrFail();

        $attendance->markBeacon([
            'missed' => $this->toBoolean($request->input('missed')),
            'lat' => $this->toFloat($request->input('lat')),
            'lng' => $this->toFloat($request->input('lng')),
            'geo_confidence' => $this->toFloat($request->input('geo_confidence')),
        ]);

        return response()->json(['status' => 'ok']);
    }

    private function toFloat($value, $default = null): ?float
    {
        if ($value === null || $value === '') {
            return $default;
        }

        return (float) $value;
    }

    private function toBoolean($value): bool
    {
        $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $bool !== null ? $bool : false;
    }
}
