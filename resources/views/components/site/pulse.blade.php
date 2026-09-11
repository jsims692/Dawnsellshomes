{{-- Feature-usage beacon: window.dsT('event', {meta}) fires a sendBeacon
     to /t. First-party, anonymous, no cookies. Auto-fires one 'page'
     event per view; feature code sprinkles the rest. --}}
<script>
window.dsT = function (e, m) {
  try {
    navigator.sendBeacon('/t', new Blob(
      [JSON.stringify({ e: e, m: m || {}, p: location.pathname })],
      { type: 'application/json' }));
  } catch (x) {}
};
document.addEventListener('DOMContentLoaded', function () { dsT('page'); });
</script>
