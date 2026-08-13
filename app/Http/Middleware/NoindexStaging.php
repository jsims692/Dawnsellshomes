<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoindexStaging
{
    /**
     * Keep search engines away from the staging domain. The real domain is
     * unaffected: the header is only sent for *.on-forge.com hosts, so the
     * eventual dawnsellshomes.com cutover needs no change here.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (str_ends_with($request->getHost(), '.on-forge.com')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
