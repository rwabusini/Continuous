<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use App\Services\QrTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TrainerSessionController extends Controller
{
    public function show(TrainingSession $session, QrTokenService $tokens): View
    {
        $payload = [
            'onsite' => $tokens->make($session, 'onsite'),
            'remote' => $session->mode !== 'onsite'
                ? $tokens->make($session, 'remote')
                : null,
        ];

        return view('trainer.sessions.show', compact('session', 'payload'));
    }

    public function challenge(TrainingSession $session): JsonResponse
    {
        $keyword = Str::upper(Str::random(6));

        Cache::put("session:{$session->id}:challenge", $keyword, now()->addMinutes(5));

        return response()->json(['keyword' => $keyword]);
    }
}
