@props(['page' => null, 'head' => null, 'title' => null])
<!DOCTYPE html>
<html lang="en">
<head>{!! $head !!}@isset($headExtra){!! $headExtra !!}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="/favicon.ico" sizes="any"><link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32.png">
<meta property="og:image" content="https://dawnsellshomes.com/images/og-image-2.jpg"><meta property="og:image:width" content="1200"><meta property="og:image:height" content="630"><meta name="twitter:card" content="summary_large_image">
<style>
  :root { --navy:#1B3A6B; --navy-dark:#0D2349; --red:#CC0000; --gold:#C8A84B; }
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:Georgia,serif; color:#222; background:#fff; }
  a { color: var(--red); }
  .nav { background: var(--navy); padding: 0 24px; display: flex; align-items: center; justify-content: space-between; height: 64px; position: sticky; top: 0; z-index: 100; }
  .nav-brand { color: #fff; font-size: 18px; font-weight: 800; text-decoration: none; font-family: Arial, sans-serif; }
  .nav-brand span { color: var(--gold); }
  .nav-links { display: flex; gap: 24px; }
  .nav-links a { color: rgba(255,255,255,.8); text-decoration: none; font-family: Arial, sans-serif; font-size: 13px; font-weight: 600; letter-spacing: .5px; text-transform: uppercase; }
  .nav-links a:hover { color: #fff; }
  footer { background: var(--navy-dark); color: rgba(255,255,255,.6); text-align: center; padding: 32px 24px; font-family: Arial, sans-serif; font-size: 13px; line-height: 1.8; margin-top: 40px; }
  footer a { color: rgba(255,255,255,.7); }
  @media(max-width:760px){ .nav-links { gap: 14px; } }
</style>
@endisset @livewireStyles</head>
<body>
<x-site.nav />
{{ $slot }}
<x-site.footer />
<x-site.text-josh />
@livewireScripts
</body>
</html>
