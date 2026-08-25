<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\HomeValueController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\SoldController;
use Illuminate\Support\Facades\Route;

// Sitewide contact form (posts to "/" with form-name=contact, matching the
// contract the original Netlify form used).
Route::post('/', [ContactController::class, 'store']);

Route::get('/sitemap.xml', SitemapController::class);

// Homes We've Sold: DB-driven page + JSON payload for the interactive map
Route::get('/sold', [SoldController::class, 'show']);
Route::get('/sold/map-data', [SoldController::class, 'data']);

// IDX listings (MLS GRID / MRED). Enabled via LISTINGS_ENABLED once approved;
// demo rows preview the display for MRED's compliance review meanwhile.
if (config('site.listings_enabled')) {
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/map-data', [ListingController::class, 'mapData'])->middleware('throttle:60,1');
    Route::get('/listings/{listingId}', [ListingController::class, 'show']);
}

// Homepage redesign preview (Pat's design, wired): unlinked + noindexed until approved
Route::get('/preview/home', function () {
    $page = App\Models\Page::where('path', '')->firstOrFail();
    $head = str_replace('<!--STYLE-->', '', $page->head_html);
    return view('preview.home', ['page' => $page, 'head' => $head]);
});

// The old one-page homepage, parked here while design v2 is the live "/":
// unlinked + noindexed, kept fully functional (injected widget + sales map).
Route::get('/old-home', [PageController::class, 'legacyHome']);

// Saved searches: consumers save their /listings filters for email alerts
Route::post('/listings/alerts', [App\Http\Controllers\SavedSearchController::class, 'store'])->middleware('throttle:10,1');
Route::get('/alerts/unsubscribe/{token}', [App\Http\Controllers\SavedSearchController::class, 'unsubscribe']);
Route::get('/alerts/manage/{token}', [App\Http\Controllers\SavedSearchController::class, 'manage']);

// Neighborhood & subdivision directory (hand-built pages + MLS-derived ones;
// individual /neighborhoods/{slug} URLs resolve via the content catch-all)
Route::get('/neighborhoods', [PageController::class, 'neighborhoods']);

// Homepage home-value widget: nearby closed sales for a lat/lng
Route::get('/home-value/nearby', [HomeValueController::class, 'nearby'])->middleware('throttle:60,1');

// IndexNow ownership verification: the key must be retrievable as {key}.txt
// at the site root for search engines to trust our URL submissions.
Route::get('/{key}.txt', function (string $key) {
    abort_unless($key !== '' && $key === config('services.indexnow.key'), 404);

    return response(config('services.indexnow.key'), 200, ['Content-Type' => 'text/plain']);
})->where('key', '[A-Za-z0-9-]+');

// Legacy .html URLs 301 to the canonical extensionless form
// (the live site resolves both; canonicals/sitemap have been extensionless since July 2026).
Route::get('/{path}.html', function (string $path) {
    return redirect('/'.$path, 301);
})->where('path', '.+');

Route::get('/', [PageController::class, 'show'])->defaults('path', '');

// All content pages: cities/x, neighborhoods/x, condos/x, schools/x, blog/x, root one-offs
Route::get('/{path}', [PageController::class, 'show'])->where('path', '[A-Za-z0-9\-_/]+');
