<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\TrainingSession;
use Closure;
use Illuminate\Http\Request;

class EnsureSessionIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $sessionId = $request->route('session');
        $session = TrainingSession::where('lms_session_id', $sessionId)->firstOrFail();

        abort_unless($session->isActive(), 403, 'Session inactive');

        $request->attributes->set('training_session', $session);

        return $next($request);
    }
}
