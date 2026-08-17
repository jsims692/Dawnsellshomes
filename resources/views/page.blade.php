<!DOCTYPE html>
<html lang="en">
<head>{!! $head !!}@if($needsAlpine ?? false)@livewireStyles @endif</head>
<body>{!! $page->body_html !!}@if($needsAlpine ?? false)
@livewireScripts
@endif</body>
</html>
