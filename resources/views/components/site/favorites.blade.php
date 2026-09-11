{{-- Sitewide saved-homes ("hearts"): favorites live in the visitor's
     localStorage — no account, no friction, instant on every card. Any
     element with .fav-btn[data-id][data-url] becomes a toggle; the nav
     badge (#favNav) appears once something is saved; /saved renders the
     list with live data. --}}
<style>
  .fav-heart { position:absolute; top:8px; right:8px; z-index:3; width:34px; height:34px; border-radius:50%; border:0; background:rgba(8,13,20,.45); color:#fff; font-size:16px; line-height:34px; text-align:center; cursor:pointer; padding:0; transition:transform .12s, background .12s, color .12s; }
  .fav-heart:hover { transform:scale(1.12); }
  .fav-heart.on { background:#fff; color:#C8102E; }
  .ld-share.fav-btn.on { border-color:#C8102E; color:#C8102E; }
  .dshl-photo { position:relative; }
  #favNav span { font-variant-numeric:tabular-nums; }
</style>
<script>
window.dsFav = {
  key: 'ds-favs',
  all: function () { try { return JSON.parse(localStorage.getItem(this.key)) || {}; } catch (e) { return {}; } },
  persist: function (m) { try { localStorage.setItem(this.key, JSON.stringify(m)); } catch (e) {} this.badge(); },
  has: function (id) { return !!this.all()[id]; },
  toggle: function (id, url) {
    var m = this.all();
    if (m[id]) { delete m[id]; } else { m[id] = { u: url || '', t: Date.now() }; if (window.dsT) dsT('heart'); }
    this.persist(m);
    return !!m[id];
  },
  count: function () { return Object.keys(this.all()).length; },
  badge: function () {
    var el = document.getElementById('favNav');
    if (!el) return;
    var n = this.count();
    el.hidden = n === 0 && !el.dataset.always;
    var s = el.querySelector('span');
    if (s) s.textContent = n;
  },
  wire: function (root) {
    var self = this;
    (root || document).querySelectorAll('.fav-btn[data-id]').forEach(function (b) {
      if (b.__fav) return; b.__fav = 1;
      if (self.has(b.dataset.id)) b.classList.add('on');
      var lbl = b.querySelector('i');
      if (lbl) lbl.textContent = self.has(b.dataset.id) ? 'Saved' : 'Save';
      b.addEventListener('click', function (e) {
        e.preventDefault(); e.stopPropagation();
        var on = self.toggle(b.dataset.id, b.dataset.url);
        b.classList.toggle('on', on);
        if (lbl) lbl.textContent = on ? 'Saved' : 'Save';
      });
    });
  },
};
document.addEventListener('DOMContentLoaded', function () { dsFav.wire(); dsFav.badge(); });
</script>
