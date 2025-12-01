<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AttachRiskContext
{
    public function handle(Request $request, Closure $next)
    {
        $request->attributes->set('risk_context', [
            'ip_hash' => hash('sha256', $request->ip()),
            'geo_confidence' => $request->float('geo_confidence', 0),
            'flags' => $request->input('flags', []),
        ]);

        return $next($request);
    }
}
