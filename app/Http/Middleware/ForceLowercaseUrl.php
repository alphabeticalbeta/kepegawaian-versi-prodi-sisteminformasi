<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceLowercaseUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->getRequestUri();
        $lower = strtolower($uri);
        if ($uri !== $lower) {
            // Keep query string
            $query = $request->getQueryString();
            $redirectTo = $lower . ($query ? '?' . $query : '');
            return redirect($redirectTo, 301);
        }
        return $next($request);
    }
}
