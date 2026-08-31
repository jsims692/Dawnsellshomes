@props(['page' => null, 'head' => null, 'title' => null])
<!DOCTYPE html>
<html lang="en">
@php $hx = isset($headExtra) ? (string) $headExtra : null; @endphp
<head>{!! $head !!}@if($hx !== null){!! $hx !!}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="/favicon.ico" sizes="any"><link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32.png">
{{-- Default social card only when the page didn't provide its own (two
     og:images confuse some messengers into the wrong crop) --}}
@if(! str_contains($hx, 'og:image'))<meta property="og:image" content="https://dawnsellshomes.com/images/og-image-2.jpg"><meta property="og:image:width" content="1200"><meta property="og:image:height" content="630">@endif<meta name="twitter:card" content="summary_large_image">
@endif
{{-- Sitewide analytics; imported heads that already carry the tag (the homepage) are left to it. --}}
@if(! str_contains($head ?? '', 'googletagmanager'))
<script async src="https://www.googletagmanager.com/gtag/js?id=G-PC0KNJJZNK"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','G-PC0KNJJZNK');</script>
@endif
{{-- Who we are, machine-readable: connects the site to the team for Google and AI crawlers. --}}
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'RealEstateAgent',
    'name' => 'Dawn Simmons Team',
    'alternateName' => 'Dawn Simmons Team — RE/MAX Suburban',
    'url' => 'https://dawnsellshomes.com',
    'telephone' => '+12246284013',
    'email' => 'josh@dawnsellshomes.com',
    'areaServed' => ['@type' => 'Place', 'name' => 'Northwest Suburbs of Chicago, Illinois'],
    'parentOrganization' => ['@type' => 'Organization', 'name' => 'RE/MAX Suburban'],
    'sameAs' => ['https://www.instagram.com/joshsimmonsre/', 'https://www.facebook.com/joshua.simmons'],
], JSON_UNESCAPED_SLASHES) !!}</script>
{{-- Design v2 ("ink & red"): fonts + stylesheet load after the DB head so v2 wins ties against any imported page CSS. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800&family=Fraunces:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/site-v2.css">
<style>[x-cloak]{display:none!important}</style>
@livewireStyles</head>
<body>
<x-site.plat-bg />
<x-site.nav />
<main>{{ $slot }}</main>
<x-site.footer />
<x-site.text-josh />
@livewireScripts
</body>
</html>
