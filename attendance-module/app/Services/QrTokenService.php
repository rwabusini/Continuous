<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TrainingSession;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use RuntimeException;

class QrTokenService
{
    public function make(TrainingSession $session, string $mode, ?string $userId = null): string
    {
        $payload = [
            'jti' => (string) Str::uuid(),
            'session' => $session->lms_session_id,
            'mode' => $mode,
            'uid' => $userId,
            'ts' => now()->timestamp,
            'exp' => now()->addSeconds(45)->timestamp,
        ];

        return Crypt::encryptString(json_encode($payload));
    }

    public function validate(string $token): object
    {
        $payload = json_decode(Crypt::decryptString($token), false);

        if (!$payload || $payload->exp < now()->timestamp) {
            throw new RuntimeException('QR token expired or invalid.');
        }

        return $payload;
    }
}
