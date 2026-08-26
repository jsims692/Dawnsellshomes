<header class="header">
  <div class="wrap">
    <nav class="nav" id="nav">
      <a class="brand" href="/" aria-label="Dawn Simmons Team home">
        <span class="brand-name">DAWN SIMMONS TEAM</span>
        <span class="brand-sub">RE/MAX Suburban</span>
      </a>
      <button class="menu-btn" id="menuBtn" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
      <div class="nav-links" id="navLinks">
        <a href="/sell">Sell</a>
        <a href="/buy">Buy</a>
        <a href="/#neighborhoods">Neighborhoods</a>
        <a href="/blog">Blog</a>
        <a href="/listings">Search Homes</a>
        <a href="/contact">Contact</a>
        <a class="btn btn--primary" href="/sell">Free Home Valuation</a>
      </div>
    </nav>
  </div>
</header>
<script>
(function () {
  var nav = document.getElementById('nav'), btn = document.getElementById('menuBtn');
  if (!nav || !btn) return;
  btn.addEventListener('click', function () {
    var open = nav.classList.toggle('open');
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
  document.querySelectorAll('#navLinks a').forEach(function (a) {
    a.addEventListener('click', function () { nav.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); });
  });
})();
</script>
