<x-site.layout :page="null" :head="null">
<x-slot:headExtra>
<title>Saved Homes | The Dawn Simmons Team – RE/MAX Suburban</title>
<meta name="robots" content="noindex,nofollow">
</x-slot:headExtra>
<style>
  .sv-wrap { max-width:1180px; margin:0 auto; padding:36px 24px 64px; font-family:Arial,sans-serif; }
  .sv-wrap h1 { font-family:'Fraunces',Georgia,serif; font-size:clamp(26px,4vw,38px); color:#0F1E2E; margin:0 0 6px; }
  .sv-sub { color:#48586B; font-size:14.5px; margin:0 0 26px; }
  .sv-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }
  .sv-card { display:block; background:#fff; border:1px solid #e0e4ed; border-radius:10px; overflow:hidden; text-decoration:none; color:#222; transition:box-shadow .15s; }
  .sv-card:hover { box-shadow:0 8px 28px rgba(15,30,46,.16); }
  .sv-photo { aspect-ratio:3/2; background:#e9edf3 center/cover no-repeat; position:relative; }
  .sv-status { position:absolute; top:10px; left:10px; background:#0F1E2E; color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:4px; }
  .sv-body { padding:16px; }
  .sv-price { font-size:21px; font-weight:800; color:#0F1E2E; }
  .sv-meta { font-size:13px; color:#666; margin-top:4px; }
  .sv-addr { font-size:14px; color:#444; margin-top:4px; }
  .sv-gone { opacity:.75; }
  .sv-gone .sv-photo { background:#DDE3EA center/cover no-repeat; }
  .sv-empty { text-align:center; padding:70px 20px; color:#48586B; }
  .sv-empty a { display:inline-block; margin-top:16px; background:#C8102E; color:#fff; border-radius:999px; padding:12px 24px; font-weight:700; text-decoration:none; }
  .sv-note { font-size:12px; color:#8A99AA; margin-top:28px; line-height:1.6; }
</style>

<div class="sv-wrap">
  <h1>&#9829; Your saved homes</h1>
  <p class="sv-sub">Saved on this device &mdash; prices and statuses below are live from the MLS. Tap a heart to remove one.</p>
  <div class="sv-grid" id="svGrid"></div>
  <div class="sv-empty" id="svEmpty" hidden>
    <div style="font-size:44px;">&#9829;</div>
    <p style="font-size:16px;max-width:46ch;margin:10px auto 0;">Nothing saved yet. Tap the heart on any home while you browse and it'll be waiting for you here.</p>
    <a href="/listings">Browse homes for sale</a>
  </div>
  <p class="sv-note" id="svNote" hidden>Listings courtesy of MRED as distributed by MLS GRID. All information deemed reliable but not guaranteed and should be independently verified.</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var favNav = document.getElementById('favNav');
  if (favNav) { favNav.dataset.always = '1'; favNav.hidden = false; }
  render();

  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  async function render() {
    var favs = dsFav.all();
    var ids = Object.keys(favs).sort(function (a, b) { return (favs[b].t || 0) - (favs[a].t || 0); });
    var grid = document.getElementById('svGrid'), empty = document.getElementById('svEmpty'), note = document.getElementById('svNote');
    grid.innerHTML = '';
    if (!ids.length) { empty.hidden = false; note.hidden = true; return; }
    empty.hidden = true; note.hidden = false;

    var found = {};
    try {
      (await (await fetch('/listings/by-ids?ids=' + ids.join(','))).json()).forEach(function (l) { found[l.id] = l; });
    } catch (e) {}

    ids.forEach(function (id) {
      var l = found[id];
      var el = document.createElement(l ? 'a' : 'div');
      el.className = 'sv-card' + (l ? '' : ' sv-gone');
      if (l) el.href = l.url;
      el.innerHTML =
        '<div class="sv-photo"' + (l && l.ph ? ' style="background-image:url(\'' + esc(l.ph) + '\')"' : '') + '>'
        + '<span class="sv-status">' + (l ? esc(l.status) : 'No longer listed') + '</span>'
        + '<button type="button" class="fav-btn fav-heart on" data-id="' + esc(id) + '" aria-label="Remove from saved">&#9829;</button>'
        + '</div>'
        + '<div class="sv-body">'
        + '<div class="sv-price">' + (l && l.price ? '$' + Number(l.price).toLocaleString() : (l ? 'See details' : '&mdash;')) + '</div>'
        + (l ? '<div class="sv-meta">' + l.beds + ' bd &middot; ' + esc(l.ba) + ' ba' + (l.sqft ? ' &middot; ' + Number(l.sqft).toLocaleString() + ' sqft' : '') + '</div>' : '')
        + '<div class="sv-addr">' + (l ? esc(l.addr) : 'This home has left the market &mdash; ask us what happened to it.') + '</div>'
        + '</div>';
      grid.appendChild(el);
    });

    dsFav.wire(grid);
    // Removing a heart on this page also removes the card.
    grid.querySelectorAll('.fav-btn').forEach(function (b) {
      b.addEventListener('click', function () { setTimeout(render, 150); });
    });
  }
});
</script>
</x-site.layout>
