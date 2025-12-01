<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AttachRiskContext
{
    public function handle(Request $request, Closure $next)
    {
        $geoConfidence = $request->input('geo_confidence');

        $request->attributes->set('risk_context', [
            'ip_hash' => hash('sha256', $request->ip()),
            'geo_confidence' => $geoConfidence === null || $geoConfidence === ''
                ? 0.0
                : (float) $geoConfidence,
            'flags' => $request->input('flags', []),
        ]);

        return $next($request);
    }
}
