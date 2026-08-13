<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectTrailingSlash
{
    /**
     * 301 trailing-slash URLs to their canonical form, matching production
     * Netlify behavior. Must inspect the raw URI: Laravel strips the trailing
     * slash during route matching, so controllers never see it.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $path = parse_url($request->getRequestUri(), PHP_URL_PATH) ?? '/';

        if ($path !== '/' && str_ends_with($path, '/') && $request->isMethod('GET')) {
            $query = $request->getQueryString();

            return redirect(rtrim($path, '/').($query ? '?'.$query : ''), 301);
        }

        return $next($request);
    }
}
