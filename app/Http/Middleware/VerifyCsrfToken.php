<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Closure;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // ajax validate
        if (request()->ajax() === false) {
            return response()->json(['message' => 'request method should be XmlHttpRequest'], 405);
        }

        // refer validate
        $referer = $request->headers->get('referer')??'';

        if (strpos($referer, env('APP_URL')) === false) {
            return response()->json(['message' => 'Bad Request'], 400);
        }

        if ($request->getMethod() === 'GET') {
            return $next($request);
        }

        // csrf validate
        $headerToken = $this->getTokenFromRequest($request);
        $cookieToken = $_COOKIE['X-CSRF-Token'] ??  null;

        if ($headerToken === null || $cookieToken === null || $headerToken !== $cookieToken) {
            return response()->json([
                'success' => false,
                'message' => 'CSRF FAIL',
                'value' => ''
            ], 400);
        }

        return $next($request);
    }
}
