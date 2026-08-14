<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // The public contact form posts as plain urlencoded (no Blade, no CSRF
        // token) — same as it did on Netlify. Honeypot + storage-first handling
        // in ContactController compensate.
        $middleware->validateCsrfTokens(except: ['/']);
        $middleware->web(prepend: [
            \App\Http\Middleware\RedirectTrailingSlash::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\NoindexStaging::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
