<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        return self::apply($request, $next($request));
    }

    public static function apply(Request $request, Response $response): Response
    {

        // NIM: 252410102046
        


        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'none'; ".
            "script-src 'self'; ".
            "style-src 'self'; ".
            "img-src 'self'; ".
            "font-src 'self'; ".
            "connect-src 'self'; ".
            "object-src 'none'; ".
            "base-uri 'self'; ".
            "form-action 'self'; ".
            "frame-src 'none'; ".
            "frame-ancestors 'none'; ".
            "worker-src 'none'; ".
            "manifest-src 'self';"
        );

        $response->headers->set(
            'X-Content-Type-Options', 'nosniff'
        );

        $response->headers->set(
            'X-Frame-Options', 'DENY'
        );

        $response->headers->set(
            'Referrer-Policy', 'strict-origin-when-cross-origin'
        );

        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=()'
        );

        $response->headers->set(
            'Cache-Control', 'no-store, private, max-age=0'
        );

        $response->headers->remove('X-Powered-By');

        if ($response->isRedirection()
            && $response->headers->has('Location')) {
            $response->setContent('');
            $response->headers->remove('Content-Length');
        }

        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000'
            );
        }

        return $response;
    }
}
