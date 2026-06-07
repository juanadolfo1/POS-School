<?php

namespace App\Http\Middleware;

use App\Models\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'login_attempts:' . $request->ip();
        $attempts = (int) Cache::get($key, 0);

        if ($attempts >= 5) {
            return response()
                ->json(ApiResponse::badRequest('Demasiados intentos de inicio de sesión. Intenta de nuevo en 60 segundos.', []))
                ->setStatusCode(429)
                ->header('Retry-After', 60);
        }

        $response = $next($request);

        // Incrementar solo si el login falló (no 200)
        if ($response->getStatusCode() !== 200) {
            Cache::put($key, $attempts + 1, 60);
        } else {
            Cache::forget($key);
        }

        return $response;
    }
}
