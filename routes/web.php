<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Sitewide contact form (posts to "/" with form-name=contact, matching the
// contract the original Netlify form used).
Route::post('/', [ContactController::class, 'store']);

Route::get('/sitemap.xml', SitemapController::class);

// Legacy .html URLs 301 to the canonical extensionless form
// (the live site resolves both; canonicals/sitemap have been extensionless since July 2026).
Route::get('/{path}.html', function (string $path) {
    return redirect('/'.$path, 301);
})->where('path', '.+');

Route::get('/', [PageController::class, 'show'])->defaults('path', '');

// All content pages: cities/x, neighborhoods/x, condos/x, schools/x, blog/x, root one-offs
Route::get('/{path}', [PageController::class, 'show'])->where('path', '[A-Za-z0-9\-_/]+');
