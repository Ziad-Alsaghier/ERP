<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class DebugRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $start = microtime(true);

        $response = $next($request);

        $time = microtime(true) - $start;

        Log::info('REQUEST_DEBUG', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'time' => $time,
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
        ]);

        return $response;
    }
}
