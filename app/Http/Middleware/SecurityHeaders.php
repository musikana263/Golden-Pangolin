<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $headers = $response->headers;

        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('X-XSS-Protection', '0');
        $headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(self), payment=(), interest-cohort=()'
        );

        if (app()->environment('production')) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        if (! $headers->has('Content-Security-Policy')) {
            $csp = [
                "default-src 'self'",
                "base-uri 'self'",
                "frame-ancestors 'self'",
                "form-action 'self'",
                "img-src 'self' data: blob: https:",
                "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com",
                "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com",
                "connect-src 'self'",
                "frame-src 'none'",
                "object-src 'none'",
                "upgrade-insecure-requests",
            ];
            $headers->set('Content-Security-Policy', implode('; ', $csp));
        }

        $headers->remove('X-Powered-By');
        $headers->remove('Server');

        return $response;
    }
}
