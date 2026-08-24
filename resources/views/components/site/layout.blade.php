@props(['page' => null, 'head' => null, 'title' => null])
<!DOCTYPE html>
<html lang="en">
<head>{!! $head !!}@isset($headExtra){!! $headExtra !!}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="/favicon.ico" sizes="any"><link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32.png">
<meta property="og:image" content="https://dawnsellshomes.com/images/og-image-2.jpg"><meta property="og:image:width" content="1200"><meta property="og:image:height" content="630"><meta name="twitter:card" content="summary_large_image">
@endisset
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
