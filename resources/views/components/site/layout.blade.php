@props(['page', 'head'])
<!DOCTYPE html>
<html lang="en">
<head>{!! $head !!}@livewireStyles</head>
<body>
<x-site.nav />
{{ $slot }}
<x-site.footer />
<x-site.text-josh />
@livewireScripts
</body>
</html>
