{{-- Global plat-map background: dimmed parcel grid behind every page.
     Red = sold, yellow = buyer-side, blue = for sale; parcels pulse at
     random and on scroll. Styles live in public/css/site-v2.css
     (#plat-bg, .lot, section translucency, z-index layering). --}}
<div id="plat-bg" aria-hidden="true"></div>
<script>
// ===== Global plat-map background: dimmed parcel grid, red = sold,
// yellow = buyer, blue = for sale; parcels pulse at random and on scroll =====
(function () {
  var host = document.getElementById('plat-bg');
  if (!host) return;
  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var NS = 'http://www.w3.org/2000/svg';
  var W = 1440, H = 2100;
  var svg = document.createElementNS(NS, 'svg');
  svg.setAttribute('viewBox', '0 0 ' + W + ' ' + H);
  svg.setAttribute('preserveAspectRatio', 'xMidYMid slice');
  svg.setAttribute('aria-hidden', 'true');
  host.appendChild(svg);

  function R(a, b) { return a + Math.random() * (b - a); }
  function el(name, attrs) {
    var n = document.createElementNS(NS, name);
    for (var k in attrs) n.setAttribute(k, attrs[k]);
    return n;
  }

  // Street grid (thin) + a couple of dashed centerlines and a curving road
  var streets = el('g', { stroke: '#0F1E2E', 'stroke-opacity': '.055', fill: 'none', 'stroke-width': '1.1' });
  svg.appendChild(streets);

  var lots = [];
  var cols = 6, rows = 9, gx = 30, gy = 34;
  var bw = (W - gx * (cols + 1)) / cols, bh = (H - gy * (rows + 1)) / rows;
  for (var r = 0; r < rows; r++) {
    for (var c = 0; c < cols; c++) {
      var x = gx + c * (bw + gx), y = gy + r * (bh + gy);
      streets.appendChild(el('rect', { x: x - gx / 2, y: y - gy / 2, width: bw + gx, height: bh + gy, rx: 2 }));
      var k = 2 + Math.floor(Math.random() * 3); // 2-4 lots per row
      var lw = bw / k, lh = bh / 2;
      for (var rr = 0; rr < 2; rr++) {
        for (var i = 0; i < k; i++) {
          var x0 = x + i * lw, y0 = y + rr * lh;
          var d = 'M' + (x0 + R(-2, 2)) + ' ' + (y0 + R(-2, 2)) +
            ' L' + (x0 + lw + R(-2, 2)) + ' ' + (y0 + R(-2, 2)) +
            ' L' + (x0 + lw + R(-2, 2)) + ' ' + (y0 + lh + R(-2, 2)) +
            ' L' + (x0 + R(-2, 2)) + ' ' + (y0 + lh + R(-2, 2)) + ' Z';
          var p = el('path', { d: d, fill: 'none', stroke: '#0F1E2E', 'stroke-width': '1.1' });
          p.setAttribute('class', 'lot');
          p.style.strokeOpacity = .05;
          svg.appendChild(p);
          lots.push(p);
        }
      }
    }
  }
  streets.appendChild(el('path', { d: 'M-40 ' + (H * .32) + ' C ' + (W * .3) + ' ' + (H * .28) + ', ' + (W * .55) + ' ' + (H * .40) + ', ' + (W + 40) + ' ' + (H * .36), 'stroke-width': '2.2' }));
  streets.appendChild(el('path', { d: 'M' + (W * .5) + ' -20 L ' + (W * .47) + ' ' + (H + 20), 'stroke-dasharray': '3 7' }));
  streets.appendChild(el('path', { d: 'M-20 ' + (H * .68) + ' L ' + (W + 20) + ' ' + (H * .66), 'stroke-dasharray': '3 7' }));

  // Color a random subset: red = sold, yellow = buyer-side, blue = for sale
  var palette = ['#C8102E', '#E8B93B', '#3E7FBE'];
  var marked = [];
  var idx = lots.map(function (_, i) { return i; });
  for (var m = idx.length - 1; m > 0; m--) { var j = Math.floor(Math.random() * (m + 1)); var t = idx[m]; idx[m] = idx[j]; idx[j] = t; }
  idx.slice(0, 27).forEach(function (li, n) {
    var lot = lots[li], color = palette[n % 3];
    lot.setAttribute('stroke', color);
    lot.setAttribute('fill', color);
    lot.setAttribute('stroke-width', '1.7');
    lot.style.strokeOpacity = reduced ? .18 : .12;
    lot.style.fillOpacity = reduced ? .05 : .03;
    marked.push(lot);
  });

  if (reduced) return; // static, dimmed, no motion

  function pulse(lot) {
    lot.style.strokeOpacity = .42;
    lot.style.fillOpacity = .10;
    setTimeout(function () { lot.style.strokeOpacity = .12; lot.style.fillOpacity = .03; }, 1900);
  }
  function pulseRandom(n) { for (var i = 0; i < n; i++) pulse(marked[Math.floor(Math.random() * marked.length)]); }

  setInterval(function () { pulseRandom(Math.random() < .3 ? 2 : 1); }, 1500);
  pulseRandom(3);

  // Scroll: gentle parallax + a pulse every ~180px travelled
  var lastY = 0, lastPulseY = 0, ticking = false;
  window.addEventListener('scroll', function () {
    lastY = window.scrollY || 0;
    if (!ticking) {
      ticking = true;
      requestAnimationFrame(function () {
        ticking = false;
        var overflow = Math.max(host.offsetHeight - window.innerHeight, 0);
        var shift = Math.min(lastY * .05, overflow);
        svg.style.transform = 'translate3d(0,' + (-shift) + 'px,0)';
        if (Math.abs(lastY - lastPulseY) > 180) { lastPulseY = lastY; pulseRandom(1); }
      });
    }
  }, { passive: true });
})();
</script>
