{{-- Pat's homepage redesign (Aug 2026), wired to live data. Preview route only until approved. --}}
<!DOCTYPE html>
<html lang="en">
<head>{!! $head !!}
<link rel="preconnect" href="https://fonts.googleapis.com/">
<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&amp;family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;1,9..144,450;1,9..144,600&amp;display=swap" rel="stylesheet">
<meta name="robots" content="noindex,nofollow"> {{-- preview only: remove when this becomes the live homepage --}}
<style>
/* ---------- Tokens & base ---------- */
*{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth}
:root{
  --ink:#0F1E2E; --ink-deep:#0B1622; --slate:#48586B; --faint:#8A99AA;
  --paper:#FFFFFF; --mist:#F2F5F9; --line:#DEE6EE;
  --red:#C8102E; --red-deep:#A50D24; --red-tint:#FBEBEE; --red-soft:#F1637C;
  --radius:18px;
  --shadow:0 14px 34px rgba(15,30,46,.10);
  --shadow-sm:0 4px 14px rgba(15,30,46,.07);
}
body{font-family:'Archivo',system-ui,sans-serif;color:var(--ink);background:var(--paper);line-height:1.6;-webkit-font-smoothing:antialiased}
img{max-width:100%;display:block}
a{color:inherit;text-decoration:none}
ul{list-style:none}
button{font-family:inherit}
::selection{background:rgba(200,16,46,.14)}
:focus-visible{outline:2px solid var(--red);outline-offset:3px;border-radius:4px}
.wrap{max-width:1150px;margin:0 auto;padding:0 clamp(1.25rem,4vw,2rem)}
.section{padding:clamp(4.25rem,8.5vw,7rem) 0}
.section--tight{padding:clamp(3rem,6vw,4.5rem) 0}
.section--mist{background:var(--mist)}
.section--ink{background:var(--ink);color:#E9EFF6}
section[id],div[id]{scroll-margin-top:96px}

/* ---------- Type ---------- */
.display{font-family:'Fraunces',serif;font-size:clamp(2.45rem,5.6vw,4.1rem);letter-spacing:-.015em;font-weight:600;line-height:1.08}
.display em{font-style:italic;font-weight:500;color:var(--red)}
.h2{font-family:'Fraunces',serif;font-size:clamp(1.85rem,3.4vw,2.65rem);letter-spacing:-.01em;font-weight:600;line-height:1.15}
.h3-serif{font-family:'Fraunces',serif;font-weight:600}
.lead{font-size:clamp(1.02rem,1.4vw,1.14rem);color:var(--slate);max-width:58ch}
.section--ink .lead{color:#AFC0D1}
.sec-head{max-width:740px;margin-bottom:clamp(2.2rem,4vw,3.2rem)}
.sec-head .lead{margin-top:.9rem}
.eyebrow{display:inline-flex;align-items:center;gap:.6rem;font-size:.74rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--red);margin-bottom:1.1rem}
.eyebrow::before{content:"";width:18px;height:2px;background:var(--red);flex:none}
.section--ink .eyebrow{color:var(--red-soft)}
.section--ink .eyebrow::before{background:var(--red-soft)}

/* ---------- Buttons & links ---------- */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;padding:.9rem 1.45rem;border-radius:999px;font-weight:600;font-size:.95rem;border:1.5px solid transparent;cursor:pointer;transition:transform .18s ease,background .18s ease,border-color .18s ease,color .18s ease;white-space:nowrap}
.btn:hover{transform:translateY(-1px)}
.btn--primary{background:var(--red);color:#fff}
.btn--primary:hover{background:var(--red-deep)}
.btn--ghost{border-color:rgba(15,30,46,.22);color:var(--ink);background:transparent}
.btn--ghost:hover{border-color:var(--ink)}
.btn--light{background:#fff;color:var(--ink)}
.btn--light:hover{background:#E9EFF6}
.section--ink .btn--ghost{border-color:rgba(255,255,255,.35);color:#fff}
.section--ink .btn--ghost:hover{border-color:#fff}
.link-arrow{font-weight:600;color:var(--red);display:inline-flex;gap:.4rem;align-items:center;transition:color .15s ease}
.link-arrow:hover{color:var(--red-deep)}

/* ---------- Top bar & header ---------- */
.topbar{background:var(--ink-deep);color:#C6D2DF;font-size:.82rem}
.topbar .wrap{display:flex;justify-content:space-between;align-items:center;gap:1rem;padding-top:.55rem;padding-bottom:.55rem}
.topbar strong{color:#fff;font-weight:600}
.topbar a{color:#fff;font-weight:600}
.topbar a:hover{color:var(--red-soft)}
.header{position:sticky;top:0;z-index:50;background:rgba(255,255,255,.9);backdrop-filter:blur(12px);border-bottom:1px solid var(--line)}
.nav{position:relative;display:flex;align-items:center;justify-content:space-between;gap:1.25rem;padding:.85rem 0}
.brand{display:flex;flex-direction:column;line-height:1.05}
.brand-name{font-weight:700;letter-spacing:.05em;font-size:1.02rem}
.brand-sub{font-size:.66rem;letter-spacing:.24em;color:var(--red);font-weight:700;text-transform:uppercase;margin-top:3px}
.nav-links{display:flex;align-items:center;gap:1.35rem;font-size:.93rem;font-weight:500;color:var(--slate)}
.nav-links a:hover{color:var(--ink)}
.nav-links .btn{color:#fff}
.menu-btn{display:none;flex-direction:column;gap:5px;background:none;border:0;cursor:pointer;padding:8px}
.menu-btn span{width:22px;height:2px;background:var(--ink);transition:transform .2s ease,opacity .2s ease}
@media (max-width:960px){
  .menu-btn{display:inline-flex}
  .nav-links{display:none;position:absolute;top:100%;left:calc(-1*clamp(1.25rem,4vw,2rem));right:calc(-1*clamp(1.25rem,4vw,2rem));background:#fff;border-bottom:1px solid var(--line);flex-direction:column;align-items:flex-start;padding:1.15rem clamp(1.25rem,4vw,2rem) 1.5rem;gap:1.05rem;box-shadow:var(--shadow)}
  .nav.open .nav-links{display:flex}
  .nav.open .menu-btn span:nth-child(1){transform:translateY(7px) rotate(45deg)}
  .nav.open .menu-btn span:nth-child(2){opacity:0}
  .nav.open .menu-btn span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}
}

/* ---------- Hero ---------- */
.hero{position:relative;overflow:hidden;padding:clamp(3.5rem,7vw,5.75rem) 0 clamp(3.5rem,7vw,5.5rem)}
.plat{position:absolute;top:-50px;right:-110px;width:min(58vw,660px);z-index:0;pointer-events:none;mask-image:linear-gradient(to left,#000 55%,transparent);-webkit-mask-image:linear-gradient(to left,#000 55%,transparent)}
.hero-grid{position:relative;z-index:1;display:grid;grid-template-columns:1.05fr .92fr;gap:clamp(2.25rem,5vw,4.5rem);align-items:center}
.hero-sub{margin:1.25rem 0 1.8rem}
.hero-ctas{display:flex;flex-wrap:wrap;gap:.8rem;margin-bottom:1.5rem}
.val-card{display:flex;gap:.6rem;background:#fff;border:1px solid var(--line);border-radius:16px;padding:.6rem;box-shadow:var(--shadow-sm);max-width:520px}
.val-card input{flex:1;min-width:0;border:0;outline:none;font:inherit;padding:.55rem .8rem;color:var(--ink)}
.val-card input::placeholder{color:var(--faint)}
.trust{display:flex;flex-wrap:wrap;gap:.45rem 1.5rem;margin-top:1.4rem;font-size:.86rem;color:var(--slate);font-weight:500}
.trust li{display:flex;align-items:center;gap:.5rem}
.trust li::before{content:"";width:6px;height:6px;border-radius:50%;background:var(--red);flex:none}
.ph{position:relative}
.ph-frame{position:relative;aspect-ratio:4/5;border-radius:22px;overflow:hidden;box-shadow:var(--shadow);background:var(--mist)}
.ph-frame img{width:100%;height:100%;object-fit:cover}
.ph-fallback{display:none;position:absolute;inset:0;background:linear-gradient(160deg,#182B42,#0F1E2E);color:#fff;align-items:center;justify-content:center;flex-direction:column;gap:.35rem;text-align:center;padding:1rem}
.ph-fallback .mono{font-family:'Fraunces',serif;font-style:italic;font-size:3rem;line-height:1}
.ph-fallback small{color:#9DB0C2;font-size:.8rem;letter-spacing:.08em;text-transform:uppercase}
.ph.noimg img{display:none}
.ph.noimg .ph-fallback{display:flex}
.ph-tag{position:absolute;left:-18px;bottom:28px;background:#fff;border-radius:14px;padding:.9rem 1.15rem;box-shadow:var(--shadow);max-width:280px}
.ph-tag strong{display:block;font-size:.98rem}
.ph-tag span{font-size:.78rem;color:var(--slate)}
@media (max-width:900px){
  .hero-grid{grid-template-columns:1fr}
  .ph{max-width:480px}
  .ph-tag{left:14px}
  .plat{top:auto;bottom:-140px;right:-180px;width:540px}
}

/* ---------- Stats band ---------- */
.stats{padding:2.7rem 0}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.5rem}
.stat-num{font-family:'Fraunces',serif;font-size:clamp(2rem,3.6vw,2.9rem);font-weight:600;letter-spacing:-.02em;color:#fff;line-height:1.1}
.stat-label{font-size:.85rem;color:#9DB0C2;margin-top:.2rem}
@media (max-width:760px){.stats-grid{grid-template-columns:repeat(2,1fr);gap:1.6rem 1rem}}

/* ---------- Why cards ---------- */
.grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:1.1rem}
@media (max-width:1000px){.grid-4{grid-template-columns:repeat(2,1fr)}}
@media (max-width:560px){.grid-4{grid-template-columns:1fr}}
.why-card{background:var(--mist);border-radius:var(--radius);padding:1.65rem 1.45rem;transition:transform .2s ease,box-shadow .2s ease}
.why-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-sm)}
.why-card h3{font-size:1.05rem;margin:.95rem 0 .45rem}
.why-card p{font-size:.92rem;color:var(--slate)}
.icon{width:44px;height:44px;border-radius:12px;background:#fff;display:flex;align-items:center;justify-content:center;color:var(--red);box-shadow:var(--shadow-sm);flex:none}
.icon svg{width:22px;height:22px}

/* ---------- Buy / Sell split ---------- */
.svc{display:grid;grid-template-columns:1fr 1fr;gap:1.1rem}
@media (max-width:860px){.svc{grid-template-columns:1fr}}
.svc-panel{border-radius:22px;padding:clamp(1.8rem,3.4vw,2.7rem);display:flex;flex-direction:column;gap:.95rem;min-height:300px}
.svc-panel h3{font-family:'Fraunces',serif;font-size:clamp(1.5rem,2.4vw,1.85rem);font-weight:600}
.svc-panel p{font-size:.97rem}
.svc-panel .link-arrow{margin-top:auto;padding-top:.6rem}
.svc--sell{background:var(--ink);color:#E9EFF6}
.svc--sell p{color:#AFC0D1}
.svc--sell .link-arrow{color:var(--red-soft)}
.svc--sell .link-arrow:hover{color:#fff}
.svc--buy{background:var(--mist)}
.svc--buy p{color:var(--slate)}

/* ---------- Results ---------- */
.res-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.1rem}
@media (max-width:960px){.res-grid{grid-template-columns:repeat(2,1fr)}}
@media (max-width:620px){.res-grid{grid-template-columns:1fr}}
.res-card{display:flex;flex-direction:column;gap:.85rem;background:#fff;border:1px solid var(--line);border-radius:var(--radius);padding:1.5rem 1.4rem;transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}
.res-card:hover{transform:translateY(-3px);box-shadow:var(--shadow);border-color:transparent}
.chip{align-self:flex-start;background:var(--red-tint);color:var(--red);font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;padding:.38rem .75rem;border-radius:999px}
.res-addr{font-weight:600;font-size:1.07rem;line-height:1.3}
.res-city{color:var(--slate);font-size:.88rem;margin-top:.15rem}
.res-price{font-family:'Fraunces',serif;font-size:1.75rem;font-weight:600}
.res-meta{font-size:.85rem;color:var(--slate);border-top:1px solid var(--line);padding-top:.85rem;margin-top:auto}

/* ---------- Areas ---------- */
.callout{display:inline-flex;flex-wrap:wrap;gap:.35rem;align-items:center;background:#fff;border:1px solid var(--line);padding:.7rem 1.05rem;border-radius:12px;font-size:.9rem;color:var(--slate);margin-bottom:1.7rem}
.tabs{display:flex;flex-wrap:wrap;gap:.55rem;margin-bottom:1.7rem}
.tab{padding:.55rem 1.05rem;border-radius:999px;border:1px solid var(--line);background:#fff;font-size:.88rem;font-weight:600;color:var(--slate);cursor:pointer;transition:border-color .15s ease,color .15s ease,background .15s ease}
.tab:hover{border-color:var(--ink);color:var(--ink)}
.tab[aria-selected="true"]{background:var(--ink);border-color:var(--ink);color:#fff}
.chips{display:flex;flex-wrap:wrap;gap:.5rem}
.pill{padding:.5rem .95rem;border-radius:999px;background:#fff;border:1px solid var(--line);font-size:.88rem;font-weight:500;color:var(--ink);transition:border-color .15s ease,color .15s ease}
.pill:hover{border-color:var(--red);color:var(--red)}
.chip-group{margin-bottom:1.4rem}
.chip-group h4{font-size:.72rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--faint);margin-bottom:.7rem}

/* ---------- Condos ---------- */
.condos{display:grid;gap:.7rem;max-width:880px}
details.city{border:1px solid var(--line);border-radius:14px;background:#fff;overflow:hidden}
details.city summary{list-style:none;cursor:pointer;display:flex;justify-content:space-between;align-items:center;gap:.8rem;padding:1.05rem 1.25rem;font-weight:600}
details.city summary::-webkit-details-marker{display:none}
.city-count{font-size:.8rem;color:var(--faint);font-weight:500;margin-left:.6rem}
details.city summary::after{content:"+";font-size:1.25rem;line-height:1;color:var(--red);transition:transform .2s ease;flex:none}
details.city[open] summary::after{transform:rotate(45deg)}
.city-body{padding:.15rem 1.25rem 1.2rem;display:flex;flex-wrap:wrap;gap:.5rem}

/* ---------- Search ---------- */
.search-card{background:#fff;border:1px solid var(--line);border-radius:20px;padding:1.4rem;box-shadow:var(--shadow-sm);display:grid;grid-template-columns:2fr 1fr 1fr .8fr .8fr auto;gap:.85rem;align-items:end}
.field label{display:block;font-size:.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--slate);margin-bottom:.45rem}
.field input,.field select,.field textarea{width:100%;padding:.78rem .85rem;border:1px solid var(--line);border-radius:12px;font:inherit;font-size:.93rem;background:#fff;color:var(--ink)}
.field input:focus,.field select:focus,.field textarea:focus{outline:none;border-color:var(--red);box-shadow:0 0 0 3px rgba(200,16,46,.12)}
.field textarea{min-height:120px;resize:vertical}
@media (max-width:1000px){
  .search-card{grid-template-columns:1fr 1fr}
  .search-card .field--loc,.search-card .btn{grid-column:1/-1}
}

/* ---------- Team ---------- */
.team-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.3rem}
@media (max-width:860px){.team-grid{grid-template-columns:1fr}}
.member{display:grid;grid-template-columns:170px 1fr;gap:1.4rem;background:var(--mist);border-radius:22px;padding:1.4rem;align-items:center}
.member .ph-frame{aspect-ratio:4/5;border-radius:16px;box-shadow:var(--shadow-sm)}
.member h3{font-family:'Fraunces',serif;font-size:1.42rem;font-weight:600}
.role{font-size:.7rem;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--red);margin:.3rem 0 .6rem}
.member p{font-size:.92rem;color:var(--slate);margin-bottom:.7rem}
@media (max-width:520px){.member{grid-template-columns:1fr}.member .ph{max-width:230px}}

/* ---------- Reviews ---------- */
.rev-head{text-align:center;max-width:640px;margin:0 auto clamp(2.2rem,4vw,3rem)}
.rev-score{font-family:'Fraunces',serif;font-size:clamp(2.8rem,5vw,3.8rem);font-weight:600;line-height:1}
.rev-stars-lg{color:var(--red);letter-spacing:4px;font-size:1.15rem;margin:.5rem 0 .35rem}
.rev-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.1rem}
@media (max-width:860px){.rev-grid{grid-template-columns:1fr}}
.rev-card{background:#fff;border-radius:var(--radius);padding:1.7rem 1.5rem;box-shadow:var(--shadow-sm);display:flex;flex-direction:column}
.rev-stars{color:var(--red);letter-spacing:2px;font-size:.92rem;margin-bottom:.85rem}
.rev-quote{font-family:'Fraunces',serif;font-style:italic;font-size:1.04rem;line-height:1.55}
.rev-name{margin-top:auto;padding-top:1.15rem;font-weight:600;font-size:.92rem}
.rev-role{font-size:.8rem;color:var(--slate)}

/* ---------- Blog ---------- */
.blog-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1.1rem}
@media (max-width:860px){.blog-grid{grid-template-columns:1fr}}
.blog-card{display:flex;flex-direction:column;background:#fff;border:1px solid var(--line);border-radius:var(--radius);padding:1.55rem 1.45rem;transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}
.blog-card:hover{transform:translateY(-3px);box-shadow:var(--shadow);border-color:transparent}
.blog-cat{font-size:.68rem;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--red)}
.blog-card h3{font-size:1.08rem;line-height:1.35;margin:.6rem 0 .5rem}
.blog-card p{font-size:.9rem;color:var(--slate);margin-bottom:1rem}
.blog-card .link-arrow{margin-top:auto;font-size:.9rem}

/* ---------- Video band ---------- */
.video-band .wrap{display:grid;grid-template-columns:1.25fr auto;align-items:center;gap:clamp(1.6rem,4vw,3rem)}
@media (max-width:820px){.video-band .wrap{grid-template-columns:1fr}}
.views{display:flex;flex-direction:column;gap:.9rem;align-items:flex-start}
.views .stat-num{font-size:clamp(2.4rem,4.6vw,3.4rem)}

/* ---------- Property management banner ---------- */
.pm{background:var(--mist);border-radius:22px;padding:clamp(1.7rem,3.2vw,2.5rem);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1.2rem}
.pm h3{font-family:'Fraunces',serif;font-size:clamp(1.35rem,2.2vw,1.7rem);font-weight:600;margin-bottom:.35rem}
.pm p{color:var(--slate);font-size:.95rem;max-width:52ch}
.pm-actions{display:flex;flex-wrap:wrap;gap:.7rem}

/* ---------- Contact ---------- */
.contact-grid{display:grid;grid-template-columns:.9fr 1.1fr;gap:clamp(2rem,5vw,4rem);align-items:start}
@media (max-width:880px){.contact-grid{grid-template-columns:1fr}}
.direct{margin-top:1.9rem;display:grid;gap:1.15rem}
.direct-item{display:flex;gap:.95rem;align-items:flex-start}
.direct-item .icon{width:40px;height:40px}
.direct-item strong{display:block;font-size:.97rem}
.direct-item a{font-weight:600}
.direct-item a:hover{color:var(--red)}
.direct-item span{font-size:.82rem;color:var(--slate)}
.direct-note{margin-top:1.4rem;font-size:.88rem;color:var(--slate);border-left:2px solid var(--red);padding-left:.9rem}
.form-card{background:#fff;border:1px solid var(--line);border-radius:22px;padding:clamp(1.5rem,3vw,2.2rem);box-shadow:var(--shadow-sm)}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.form-grid .full{grid-column:1/-1}
@media (max-width:560px){.form-grid{grid-template-columns:1fr}}
.form-note{font-size:.78rem;color:var(--faint);margin-top:.95rem}
.success{display:none;text-align:center;padding:2.6rem 1rem}
.success .mark{width:56px;height:56px;border-radius:50%;background:var(--red-tint);color:var(--red);display:flex;align-items:center;justify-content:center;margin:0 auto 1.1rem}
.success h3{font-family:'Fraunces',serif;font-size:1.5rem;margin-bottom:.4rem}
.success p{color:var(--slate);font-size:.95rem}
.form-card.done form{display:none}
.form-card.done .success{display:block}

/* ---------- Footer ---------- */
.footer{background:var(--ink-deep);color:#AFC0D1;padding:4rem 0 2rem;font-size:.9rem}
.foot-grid{display:grid;grid-template-columns:1.35fr 1fr 1fr 1.5fr;gap:2.2rem;margin-bottom:2.6rem}
@media (max-width:900px){.foot-grid{grid-template-columns:1fr 1fr}}
@media (max-width:560px){.foot-grid{grid-template-columns:1fr}}
.footer h4{color:#fff;font-size:.76rem;letter-spacing:.15em;text-transform:uppercase;margin-bottom:1.05rem}
.footer a:hover{color:#fff}
.footer ul{display:grid;gap:.55rem}
.foot-brand .brand-name{color:#fff}
.foot-brand p{margin-top:.9rem;font-size:.88rem;max-width:34ch}
.socials{display:flex;gap:.9rem;margin-top:1.1rem;font-weight:600}
.offices{line-height:2}
.foot-bottom{border-top:1px solid rgba(255,255,255,.12);padding-top:1.6rem;display:flex;flex-wrap:wrap;gap:.6rem 1.5rem;justify-content:space-between;font-size:.8rem;color:#7E93A8}

/* ---------- Mobile call bar ---------- */
.mob-cta{position:fixed;left:0;right:0;bottom:0;z-index:60;display:none;grid-template-columns:1fr 1fr;gap:.6rem;padding:.6rem .9rem calc(.6rem + env(safe-area-inset-bottom));background:rgba(255,255,255,.95);backdrop-filter:blur(10px);border-top:1px solid var(--line)}
@media (max-width:720px){.mob-cta{display:grid} body{padding-bottom:76px}}

/* ---------- Reveal ---------- */
.rv{opacity:0;transform:translateY(16px);transition:opacity .55s ease,transform .55s ease}
.rv.in{opacity:1;transform:none}
@media (prefers-reduced-motion:reduce){
  html{scroll-behavior:auto}
  *,*::before,*::after{transition:none!important;animation:none!important}
  .rv{opacity:1;transform:none}
}


/* wired-widget styles (added in implementation) */
.ph .photo-card{position:relative;border-radius:20px;overflow:hidden;box-shadow:0 30px 70px rgba(15,30,46,.25)}
.ph .photo-card img{display:block;width:100%;height:auto;aspect-ratio:4/5;object-fit:cover}
.ph .photo-cap{position:absolute;left:14px;right:14px;bottom:14px;background:rgba(255,255,255,.96);border-radius:14px;padding:.8rem 1rem;font-family:'Archivo',system-ui,sans-serif}
.ph .photo-cap strong{font-weight:700;color:var(--ink)}
.ph .photo-cap span{display:block;font-size:.75rem;color:var(--slate);letter-spacing:.04em;text-transform:uppercase;margin-top:2px}
.ph .photo-cap em{display:block;font-style:normal;font-size:.8rem;color:var(--faint);margin-top:4px}
.hv-preds{position:absolute;left:0;right:0;top:calc(100% + 6px);margin:0;padding:.4rem 0;list-style:none;background:#fff;border:1px solid var(--line);border-radius:12px;box-shadow:0 18px 40px rgba(15,30,46,.16);z-index:60;max-height:280px;overflow:auto;font-family:'Archivo',system-ui,sans-serif;font-size:.9rem;text-align:left}
.hv-preds li{padding:.55rem .9rem;cursor:pointer}
.hv-preds li strong{color:var(--ink)} .hv-preds li span{color:var(--faint);font-size:.8rem}
.hv-preds li.active,.hv-preds li:hover{background:var(--mist)}
.hv-preds li.hv-attrib{font-size:.65rem;color:var(--faint);text-align:right;cursor:default;padding:.2rem .8rem 0}
.hv-result{background:#fff;border:1px solid var(--line);border-radius:16px;padding:1.1rem 1.25rem;margin-top:.9rem;font-family:'Archivo',system-ui,sans-serif;text-align:left}
.hv-kicker{font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--red)}
.hv-row{display:flex;flex-wrap:wrap;align-items:baseline;gap:.7rem;margin:.35rem 0 .3rem}
.hv-median{font-family:'Fraunces',serif;font-size:2rem;font-weight:600;color:var(--ink)}
.hv-range{font-size:.85rem;color:var(--slate)}
.hv-note{font-size:.85rem;color:var(--slate);margin:.3rem 0 .7rem}
.res-photo{display:block;aspect-ratio:3/2;border-radius:12px;background:var(--mist) center/cover no-repeat;margin-bottom:.9rem}
.form-ok{margin-top:.8rem;color:#177245;font-weight:600;font-family:'Archivo',system-ui,sans-serif}
[x-cloak]{display:none!important}
html,body{overflow-x:hidden}
.hero{overflow:hidden;position:relative}
</style>
@livewireStyles
</head>
<body>


<!-- Top bar -->
<div class="topbar">
  <div class="wrap">
    <div><strong>Another DAWN Deal</strong><span class="hide-sm"> · RE/MAX Hall of Fame · {{ \App\Support\TeamStats::soldTotal() }} homes sold</span></div>
    <div>Call or text, 7 days a week &nbsp;<a href="tel:8477381884">(847) 738-1884</a></div>
  </div>
</div>

<!-- Header -->
<header class="header">
  <div class="wrap">
    <nav class="nav" id="nav">
      <a class="brand" href="#home" aria-label="Dawn Simmons Team home">
        <span class="brand-name">DAWN SIMMONS TEAM</span>
        <span class="brand-sub">RE/MAX Suburban</span>
      </a>
      <button class="menu-btn" id="menuBtn" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
      <div class="nav-links" id="navLinks">
        <a href="#services">Buy or Sell</a>
        <a href="#results">Results</a>
        <a href="#areas">Neighborhoods</a>
        <a href="#team">Team</a>
        <a href="#reviews">Reviews</a>
        <a href="/blog">Blog</a>
        <a class="btn btn--primary" href="#contact">Free Home Valuation</a>
      </div>
    </nav>
  </div>
</header>

<main id="home">

<!-- Hero -->
<section class="hero">
  <svg class="plat" viewBox="0 0 640 640" fill="none" aria-hidden="true">
    <g stroke="#0F1E2E" stroke-opacity=".08" stroke-width="1.5">
      <path d="M60 30 L600 55 L585 610 L35 585 Z"></path>
      <path d="M45 210 C 200 195, 430 228, 596 206"></path>
      <path d="M330 32 L318 606"></path>
      <path d="M150 36 L142 205"></path><path d="M245 42 L236 210"></path>
      <path d="M425 48 L432 214"></path><path d="M515 52 L522 212"></path>
      <path d="M55 330 L323 345"></path><path d="M52 455 L320 470"></path>
      <path d="M140 212 L128 583"></path><path d="M232 218 L224 588"></path>
      <path d="M330 350 L590 330"></path><path d="M326 480 L588 462"></path>
      <path d="M430 218 L438 600"></path><path d="M515 214 L524 604"></path>
      <path d="M60 95 L598 118" stroke-dasharray="3 6"></path>
    </g>
    <g>
      <path d="M143 347 L233 352 L226 470 L136 464 Z" fill="#C8102E" fill-opacity=".06" stroke="#C8102E" stroke-opacity=".45" stroke-width="1.5"></path>
      <circle cx="184" cy="400" r="4" fill="#C8102E"></circle>
      <text x="152" y="432" font-family="Archivo, sans-serif" font-size="10" letter-spacing="2" fill="#C8102E" fill-opacity=".75">SINCE 1988</text>
    </g>
  </svg>
  <div class="wrap">
    <div class="hero-grid">
      <div>
        <p class="eyebrow">Prospect Heights · Mount Prospect · Arlington Heights</p>
        <h1 class="display">A mother &amp; son who know these neighborhoods <em>by heart.</em></h1>
        <p class="lead hero-sub">For over 30 years, Dawn and her son Josh have lived here, raised a family here, and helped hundreds of neighbors move across the northwest suburbs. Two full-time local agents who treat your move like it’s their own.</p>
        <div class="hero-ctas">
          <a class="btn btn--primary" href="#contact">Get a free home valuation</a>
          <a class="btn btn--ghost" href="#team">Meet Dawn &amp; Josh</a>
        </div>
        <form class="val-card" id="valForm" aria-label="Home valuation" x-data="homeValue()" x-init="init()" @submit.prevent="submit()" style="position:relative">
          <input type="text" id="valAddress" placeholder="Enter your address — what's your home worth?" aria-label="Your home address" x-model="query" @input.debounce.220ms="suggest()" @keydown.down.prevent="move(1)" @keydown.up.prevent="move(-1)" @keydown.escape="preds=[]" autocomplete="off">
  <ul x-show="preds.length" x-cloak @mousedown.prevent class="hv-preds" role="listbox"><template x-for="(p,i) in preds" :key="p.id"><li :class="{active:i===hi}" @click="pick(p)" @mouseenter="hi=i" role="option"><strong x-text="p.main"></strong> <span x-text="p.secondary"></span></li></template><li class="hv-attrib">Powered by Google</li></ul>
          <button class="btn btn--primary" type="submit">Get value</button>

        <div x-show="result" x-cloak x-transition class="hv-result rv">
  <template x-if="result && result.ok"><div>
    <div class="hv-kicker">Homes we've sold within <span x-text="result.radius_miles"></span> mi of <span x-text="shortAddr"></span></div>
    <div class="hv-row"><span class="hv-median" x-text="fmt(result.median)"></span><span class="hv-range">typical range <strong x-text="fmt(result.low)+' – '+fmt(result.high)"></strong> · based on <strong x-text="result.count"></strong> of our own sales</span></div>
    <p class="hv-note">That's the neighborhood — not your house. Get the exact number for your home, free, usually within 24 hours.</p>
    <button type="button" class="btn btn--primary" @click="toContact()">Get my exact number →</button>
  </div></template>
  <template x-if="result && !result.ok"><div>
    <p class="hv-note">We haven't closed enough sales right there for a fair snapshot — but we'll pull real comps and send you a free valuation within 24 hours.</p>
    <button type="button" class="btn btn--primary" @click="toContact()">Get my free valuation →</button>
  </div></template>
</div></form>

        <ul class="trust">
          <li>RE/MAX Hall of Fame</li>
          <li>{{ \App\Support\TeamStats::soldTotal() }} homes sold</li>
          <li>4.9★ · 62+ Google reviews</li>
        </ul>
      </div>
      <div class="ph">
  <div class="photo-card">
    <img src="/images/hero-team.jpg" srcset="/images/hero-team.jpg 1200w, /images/hero-team@2x.jpg 2000w" sizes="(max-width:900px) 92vw, 480px" width="1200" height="1500" alt="Dawn Simmons and Josh Simmons, the mother-and-son Dawn Simmons Team at RE/MAX Suburban" fetchpriority="high">
    <div class="photo-cap"><strong>Dawn &amp; Josh</strong><span>Mom · Broker &nbsp;&mdash;&nbsp; Son · Broker Associate</span><em>Born and raised in Prospect Heights, and still here.</em></div>
  </div>
</div>
    </div>
  </div>
</section>

<!-- Stats -->
<div class="section--ink stats">
  <div class="wrap">
    <div class="stats-grid">
      <div class="rv in"><div class="stat-num">{{ \App\Support\TeamStats::soldTotal() }}</div><div class="stat-label">Homes sold</div></div>
      <div class="rv in"><div class="stat-num">38</div><div class="stat-label">Combined years of experience</div></div>
      <div class="rv in"><div class="stat-num">4.9★</div><div class="stat-label">Across 62+ Google reviews</div></div>
      <div class="rv in"><div class="stat-num">10M+</div><div class="stat-label">Views on one home tour</div></div>
    </div>
  </div>
</div>

<!-- Why us -->
<section class="section" id="why">
  <div class="wrap">
    <div class="sec-head rv in">
      <p class="eyebrow">Why the Dawn Simmons Team</p>
      <h2 class="h2">What working with us actually gets you.</h2>
      <p class="lead">There are hundreds of agents in the northwest suburbs. Here’s the difference.</p>
    </div>
    <div class="grid-4">
      <div class="why-card rv in">
        <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h3l1.5 4L7.5 9.5a11 11 0 0 0 5 5L14 12.5 18 14v3a2 2 0 0 1-2 2A13 13 0 0 1 3 6a2 2 0 0 1 2-2Z"></path></svg></div>
        <h3>7 days a week, day or night</h3>
        <p>Call or text anytime — evenings, weekends, holidays. Real estate doesn’t keep business hours, and neither do we.</p>
      </div>
      <div class="why-card rv in">
        <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"></circle><path d="M12 7.5v9M14.4 9.2c-.5-.8-1.4-1.2-2.4-1.2-1.4 0-2.5.8-2.5 2 0 2.6 5 1.5 5 4 0 1.2-1.1 2-2.5 2-1 0-1.9-.4-2.4-1.2"></path></svg></div>
        <h3>We fight for every dollar</h3>
        <p>Nobody negotiates harder. Bidding wars won for buyers, multiple offers pulled for sellers — {{ \App\Support\TeamStats::soldTotal() }} times and counting.</p>
      </div>
      <div class="why-card rv in">
        <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="13.5" height="12" rx="2.5"></rect><path d="M16.5 10.5 21 8v8l-4.5-2.5"></path></svg></div>
        <h3>Marketing that reaches millions</h3>
        <p>Professional photo and video on every listing. One of Josh’s home tours alone has topped 10 million views.</p>
      </div>
      <div class="why-card rv in">
        <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-6.5-5.2-6.5-10a6.5 6.5 0 0 1 13 0c0 4.8-6.5 10-6.5 10Z"></path><path d="M9.8 11.4 12 9.4l2.2 2M10.6 11.1V14h2.8v-2.9"></path></svg></div>
        <h3>We actually grew up here</h3>
        <p>Prospect Heights since 1988. We know which blocks flood, which schools feed where, and what your street really sells for.</p>
      </div>
    </div>
  </div>
</section>

<!-- Buy / Sell -->
<section class="section section--tight" id="services" style="padding-top:0">
  <div class="wrap">
    <div class="svc">
      <div class="svc-panel svc--sell rv">
        <p class="eyebrow">Selling</p>
        <h3>Sell your home for top dollar.</h3>
        <p>The right price, professional marketing, and sharp negotiation — a proven strategy that consistently gets sellers top dollar in Prospect Heights, Mount Prospect, and Arlington Heights. Many of our listings receive multiple offers.</p>
        <a class="link-arrow" href="#contact">Get a free home valuation →</a>
      </div>
      <div class="svc-panel svc--buy rv">
        <p class="eyebrow">Buying</p>
        <h3>Find your next home.</h3>
        <p>Full-time local agents with access to listings before they hit the market — and the relationships to help you win when it’s competitive. We’ll find the right home and fight for you through closing.</p>
        <a class="link-arrow" href="#search">Start your home search →</a>
      </div>
    </div>
  </div>
</section>

<!-- Results -->
<section class="section section--mist" id="results">
  <div class="wrap">
    <div class="sec-head rv">
      <p class="eyebrow">Recent results</p>
      <h2 class="h2">Homes are selling fast in your area.</h2>
      <p class="lead">Real results from your neighbors in Prospect Heights, Mount Prospect, and the surrounding suburbs.</p>
    </div>
    <div class="res-grid">
      <a class="res-card rv" href="/neighborhoods/prospect-manor-mount-prospect"><span class="res-photo" style="background-image:url('/images/417-prospect-manor.jpg')"></span>
        <span class="chip">Sold in 7 days</span>
        <div><div class="res-addr">417 N Prospect Manor Ave</div><div class="res-city">Mount Prospect, IL 60056</div></div>
        <div class="res-price">$449,900</div>
        <div class="res-meta">Multiple offers · Listed &amp; sold by our team</div>
      </a>
      <a class="res-card rv" href="/cities/round-lake-beach"><span class="res-photo" style="background-image:url('/images/29-glenwood.jpg')"></span>
        <span class="chip">Full price · Under a week</span>
        <div><div class="res-addr">29 Glenwood Dr</div><div class="res-city">Round Lake Beach, IL 60073</div></div>
        <div class="res-price">$265,000</div>
        <div class="res-meta">3 bd · 1 ba · Steps off the lake</div>
      </a>
      <a class="res-card rv" href="/cities/prospect-heights"><span class="res-photo" style="background-image:url('/images/2-marberry.jpg')"></span>
        <span class="chip">Closed</span>
        <div><div class="res-addr">2 Marberry Dr</div><div class="res-city">Prospect Heights, IL 60070</div></div>
        <div class="res-price">5 bd · 4 ba</div>
        <div class="res-meta">2,678 sqft · In-law suite</div>
      </a>
    </div>
  </div>
</section>

<!-- Areas -->
<div class="wrap" style="margin-top:1.4rem"><a class="link-arrow rv" href="/sold">See all 555 homes we've sold — with the interactive map →</a></div>
<section class="section" id="areas">
  <div class="wrap">
    <div class="sec-head rv">
      <p class="eyebrow">Explore by area</p>
      <h2 class="h2">Browse homes across Chicagoland.</h2>
      <p class="lead">From Chicago’s north side to Fox Lake — pick a community to see active MLS listings, updated daily.</p>
    </div>
    <div class="callout rv">🧭 New to the area? <a class="link-arrow" href="/moving-to-northwest-suburbs">Start here: the complete guide to moving to the northwest suburbs →</a></div>
    <div class="tabs" role="tablist" aria-label="Regions">
      <button class="tab" role="tab" aria-selected="true" aria-controls="area-core" id="tab-core">Your core market</button>
      <button class="tab" role="tab" aria-selected="false" aria-controls="area-north" id="tab-north">North suburbs</button>
      <button class="tab" role="tab" aria-selected="false" aria-controls="area-nw" id="tab-nw">Northwest suburbs</button>
      <button class="tab" role="tab" aria-selected="false" aria-controls="area-fox" id="tab-fox">Fox River Valley</button>
      <button class="tab" role="tab" aria-selected="false" aria-controls="area-lake" id="tab-lake">Lake County</button>
      <button class="tab" role="tab" aria-selected="false" aria-controls="area-chi" id="tab-chi">Chicago</button>
      <button class="tab" role="tab" aria-selected="false" aria-controls="area-west" id="tab-west">West &amp; southwest</button>
    </div>
    <div id="area-core" role="tabpanel" aria-labelledby="tab-core">
      <div class="chips">
        <a class="pill" href="/cities/prospect-heights">Prospect Heights</a><a class="pill" href="/cities/mount-prospect">Mount Prospect</a><a class="pill" href="/cities/arlington-heights">Arlington Heights</a><a class="pill" href="/cities/palatine">Palatine</a><a class="pill" href="/cities/wheeling">Wheeling</a><a class="pill" href="/cities/des-plaines">Des Plaines</a><a class="pill" href="/cities/rolling-meadows">Rolling Meadows</a><a class="pill" href="/cities/inverness">Inverness</a><a class="pill" href="/cities/south-barrington">South Barrington</a><a class="pill" href="/cities/fox-lake">Fox Lake</a>
      </div>
    </div>
    <div id="area-north" role="tabpanel" aria-labelledby="tab-north" hidden="">
      <div class="chips">
        <a class="pill" href="/cities/buffalo-grove">Buffalo Grove</a><a class="pill" href="/cities/northbrook">Northbrook</a><a class="pill" href="/cities/glenview">Glenview</a><a class="pill" href="/cities/deerfield">Deerfield</a><a class="pill" href="/cities/northfield">Northfield</a><a class="pill" href="/cities/winnetka">Winnetka</a><a class="pill" href="/cities/glencoe">Glencoe</a><a class="pill" href="/cities/highland-park">Highland Park</a><a class="pill" href="/cities/highwood">Highwood</a><a class="pill" href="/cities/lake-forest">Lake Forest</a><a class="pill" href="/cities/lake-bluff">Lake Bluff</a><a class="pill" href="/cities/north-chicago">North Chicago</a><a class="pill" href="/cities/waukegan">Waukegan</a>
      </div>
    </div>
    <div id="area-nw" role="tabpanel" aria-labelledby="tab-nw" hidden="">
      <div class="chips">
        <a class="pill" href="/cities/barrington">Barrington</a><a class="pill" href="/cities/lake-barrington">Lake Barrington</a><a class="pill" href="/cities/north-barrington">North Barrington</a><a class="pill" href="/cities/barrington-hills">Barrington Hills</a><a class="pill" href="/cities/hoffman-estates">Hoffman Estates</a><a class="pill" href="/cities/schaumburg">Schaumburg</a><a class="pill" href="/cities/elk-grove-village">Elk Grove Village</a><a class="pill" href="/cities/streamwood">Streamwood</a><a class="pill" href="/cities/hanover-park">Hanover Park</a><a class="pill" href="/cities/roselle">Roselle</a><a class="pill" href="/cities/bloomingdale">Bloomingdale</a><a class="pill" href="/cities/itasca">Itasca</a><a class="pill" href="/cities/wood-dale">Wood Dale</a><a class="pill" href="/cities/bensenville">Bensenville</a><a class="pill" href="/cities/addison">Addison</a><a class="pill" href="/cities/villa-park">Villa Park</a>
      </div>
    </div>
    <div id="area-fox" role="tabpanel" aria-labelledby="tab-fox" hidden="">
      <div class="chips">
        <a class="pill" href="/cities/algonquin">Algonquin</a><a class="pill" href="/cities/lake-in-the-hills">Lake in the Hills</a><a class="pill" href="/cities/cary">Cary</a><a class="pill" href="/cities/fox-river-grove">Fox River Grove</a><a class="pill" href="/cities/crystal-lake">Crystal Lake</a><a class="pill" href="/cities/carpentersville">Carpentersville</a><a class="pill" href="/cities/east-dundee">East Dundee</a><a class="pill" href="/cities/west-dundee">West Dundee</a><a class="pill" href="/cities/elgin">Elgin</a><a class="pill" href="/cities/south-elgin">South Elgin</a><a class="pill" href="/cities/st-charles">St. Charles</a><a class="pill" href="/cities/wayne">Wayne</a><a class="pill" href="/cities/bartlett">Bartlett</a>
      </div>
    </div>
    <div id="area-lake" role="tabpanel" aria-labelledby="tab-lake" hidden="">
      <div class="chips">
        <a class="pill" href="/cities/libertyville">Libertyville</a><a class="pill" href="/cities/vernon-hills">Vernon Hills</a><a class="pill" href="/cities/mundelein">Mundelein</a><a class="pill" href="/cities/gurnee">Gurnee</a><a class="pill" href="/cities/grayslake">Grayslake</a><a class="pill" href="/cities/round-lake">Round Lake</a><a class="pill" href="/cities/round-lake-beach">Round Lake Beach</a><a class="pill" href="/cities/round-lake-heights">Round Lake Heights</a><a class="pill" href="/cities/round-lake-park">Round Lake Park</a><a class="pill" href="/cities/lake-villa">Lake Villa</a><a class="pill" href="/cities/lindenhurst">Lindenhurst</a><a class="pill" href="/cities/antioch">Antioch</a><a class="pill" href="/cities/ingleside">Ingleside</a><a class="pill" href="/cities/third-lake">Third Lake</a><a class="pill" href="/cities/wauconda">Wauconda</a><a class="pill" href="/cities/island-lake">Island Lake</a><a class="pill" href="/cities/volo">Volo</a><a class="pill" href="/cities/zion">Zion</a><a class="pill" href="/cities/beach-park">Beach Park</a><a class="pill" href="/cities/winthrop-harbor">Winthrop Harbor</a>
      </div>
    </div>
    <div id="area-chi" role="tabpanel" aria-labelledby="tab-chi" hidden="">
      <div class="chip-group">
        <h4>Northwest &amp; north side</h4>
        <div class="chips">
          <a class="pill" href="/cities/edison-park">Edison Park</a><a class="pill" href="/cities/norwood-park">Norwood Park</a><a class="pill" href="/cities/portage-park">Portage Park</a><a class="pill" href="/cities/jefferson-park">Jefferson Park</a><a class="pill" href="/cities/irving-park">Irving Park</a><a class="pill" href="/cities/forest-glen">Forest Glen</a><a class="pill" href="/cities/dunning">Dunning</a><a class="pill" href="/cities/montclare">Montclare</a><a class="pill" href="/cities/belmont-cragin">Belmont Cragin</a><a class="pill" href="/cities/hermosa">Hermosa</a><a class="pill" href="/cities/avondale">Avondale</a><a class="pill" href="/cities/logan-square">Logan Square</a><a class="pill" href="/cities/bucktown">Bucktown</a><a class="pill" href="/cities/wicker-park">Wicker Park</a><a class="pill" href="/cities/west-town">West Town</a><a class="pill" href="/cities/ukrainian-village">Ukrainian Village</a><a class="pill" href="/cities/humboldt-park">Humboldt Park</a><a class="pill" href="/cities/wrigleyville">Wrigleyville</a><a class="pill" href="/cities/roscoe-village">Roscoe Village</a><a class="pill" href="/cities/north-center">North Center</a><a class="pill" href="/cities/ravenswood">Ravenswood</a><a class="pill" href="/cities/lincoln-square">Lincoln Square</a><a class="pill" href="/cities/andersonville">Andersonville</a><a class="pill" href="/cities/edgewater">Edgewater</a><a class="pill" href="/cities/rogers-park">Rogers Park</a><a class="pill" href="/cities/west-ridge">West Ridge</a><a class="pill" href="/cities/uptown">Uptown</a><a class="pill" href="/cities/lakeview">Lakeview</a><a class="pill" href="/cities/lincoln-park">Lincoln Park</a><a class="pill" href="/cities/old-town">Old Town</a><a class="pill" href="/cities/river-north">River North</a><a class="pill" href="/cities/streeterville">Streeterville</a><a class="pill" href="/cities/gold-coast">Gold Coast</a><a class="pill" href="/cities/near-north-side">Near North Side</a>
        </div>
      </div>
      <div class="chip-group">
        <h4>West &amp; southwest side</h4>
        <div class="chips">
          <a class="pill" href="/cities/austin">Austin</a><a class="pill" href="/cities/west-garfield-park">West Garfield Park</a><a class="pill" href="/cities/east-garfield-park">East Garfield Park</a><a class="pill" href="/cities/east-humboldt-park">East Humboldt Park</a><a class="pill" href="/cities/west-loop">West Loop</a><a class="pill" href="/cities/fulton-market">Fulton Market</a><a class="pill" href="/cities/near-west-side">Near West Side</a><a class="pill" href="/cities/pilsen">Pilsen</a><a class="pill" href="/cities/little-village">Little Village</a><a class="pill" href="/cities/mckinley-park">McKinley Park</a><a class="pill" href="/cities/bridgeport">Bridgeport</a><a class="pill" href="/cities/back-of-the-yards">Back of the Yards</a><a class="pill" href="/cities/brighton-park">Brighton Park</a><a class="pill" href="/cities/clearing">Clearing</a><a class="pill" href="/cities/garfield-ridge">Garfield Ridge</a><a class="pill" href="/cities/archer-heights">Archer Heights</a><a class="pill" href="/cities/gage-park">Gage Park</a><a class="pill" href="/cities/west-elsdon">West Elsdon</a><a class="pill" href="/cities/west-lawn">West Lawn</a><a class="pill" href="/cities/chicago-lawn">Chicago Lawn</a><a class="pill" href="/cities/marquette-park">Marquette Park</a><a class="pill" href="/cities/ashburn">Ashburn</a><a class="pill" href="/cities/beverly">Beverly</a><a class="pill" href="/cities/morgan-park">Morgan Park</a><a class="pill" href="/cities/mount-greenwood">Mount Greenwood</a>
        </div>
      </div>
    </div>
    <div id="area-west" role="tabpanel" aria-labelledby="tab-west" hidden="">
      <div class="chips">
        <a class="pill" href="/cities/park-ridge">Park Ridge</a><a class="pill" href="/cities/niles">Niles</a><a class="pill" href="/cities/skokie">Skokie</a><a class="pill" href="/cities/evanston">Evanston</a><a class="pill" href="/cities/morton-grove">Morton Grove</a><a class="pill" href="/cities/lincolnwood">Lincolnwood</a><a class="pill" href="/cities/harwood-heights">Harwood Heights</a><a class="pill" href="/cities/norridge">Norridge</a><a class="pill" href="/cities/franklin-park">Franklin Park</a><a class="pill" href="/cities/rosemont">Rosemont</a><a class="pill" href="/cities/river-grove">River Grove</a><a class="pill" href="/cities/elmwood-park">Elmwood Park</a><a class="pill" href="/cities/melrose-park">Melrose Park</a><a class="pill" href="/cities/bellwood">Bellwood</a><a class="pill" href="/cities/hillside">Hillside</a><a class="pill" href="/cities/westchester">Westchester</a><a class="pill" href="/cities/la-grange-park">La Grange Park</a><a class="pill" href="/cities/western-springs">Western Springs</a><a class="pill" href="/cities/hinsdale">Hinsdale</a><a class="pill" href="/cities/clarendon-hills">Clarendon Hills</a><a class="pill" href="/cities/westmont">Westmont</a><a class="pill" href="/cities/downers-grove">Downers Grove</a><a class="pill" href="/cities/lisle">Lisle</a><a class="pill" href="/cities/lombard">Lombard</a><a class="pill" href="/cities/glen-ellyn">Glen Ellyn</a><a class="pill" href="/cities/wheaton">Wheaton</a><a class="pill" href="/cities/carol-stream">Carol Stream</a><a class="pill" href="/cities/glendale-heights">Glendale Heights</a><a class="pill" href="/cities/oakbrook-terrace">Oakbrook Terrace</a><a class="pill" href="/cities/oak-brook">Oak Brook</a><a class="pill" href="/cities/elmhurst">Elmhurst</a><a class="pill" href="/cities/villa-park">Villa Park</a>
      </div>
    </div>
  </div>
</section>

<!-- Condos -->
<section class="section section--mist" id="condos" style="padding-top:clamp(3rem,6vw,4.5rem)">
  <div class="wrap">
    <div class="sec-head rv">
      <p class="eyebrow">Condos &amp; townhomes</p>
      <h2 class="h2">Condo &amp; townhome communities.</h2>
      <p class="lead">Floor plans, amenities, and pricing for the top complexes across our core market. Pick a city to browse.</p>
    </div>
    <div class="condos">
      <details class="city rv" open="">
        <summary><span>Mount Prospect <em class="city-count">4 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/colony-country-mount-prospect">Colony Country · $180K–$265K</a>
          <a class="pill" href="/condos/hunt-club-on-the-lake">Hunt Club on the Lake · $160K–$280K</a>
          <a class="pill" href="/condos/village-centre-mount-prospect">Village Centre · $250K–$390K</a>
          <a class="pill" href="/condos/evergreen-woods-mount-prospect">Evergreen Woods · $280K–$400K</a>
        </div>
      </details>
      <details class="city rv">
        <summary><span>Arlington Heights <em class="city-count">4 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/lake-arlington-towne">Lake Arlington Towne · $280K–$420K</a>
          <a class="pill" href="/condos/arlington-glen">Arlington Glen · $160K–$270K</a>
          <a class="pill" href="/condos/stone-creek-arlington-heights">Stone Creek · $140K–$180K</a>
          <a class="pill" href="/condos/lexington-heritage-arlington-heights">Lexington Heritage · $520K–$600K</a>
        </div>
      </details>
      <details class="city rv">
        <summary><span>Palatine <em class="city-count">9 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/benchmark-palatine">Benchmark · $220K–$380K</a>
          <a class="pill" href="/condos/one-renaissance-place">One Renaissance Place · $200K–$360K</a>
          <a class="pill" href="/condos/knollwood-of-palatine">Knollwood · $290K–$340K</a>
          <a class="pill" href="/condos/palatine-commons">Palatine Commons · $400K–$480K</a>
          <a class="pill" href="/condos/auburn-woods-palatine">Auburn Woods · $340K–$420K</a>
          <a class="pill" href="/condos/heritage-of-palatine">Heritage of Palatine · $260K–$360K</a>
          <a class="pill" href="/condos/forest-edge-palatine">Forest Edge · $200K–$250K</a>
          <a class="pill" href="/condos/baybrook-palatine">Baybrook · $210K–$260K</a>
          <a class="pill" href="/condos/fox-cove-palatine">Fox Cove · $150K–$200K</a>
        </div>
      </details>
      <details class="city rv">
        <summary><span>Wheeling <em class="city-count">4 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/one-milwaukee-place-wheeling">One Milwaukee Place · $220K–$360K</a>
          <a class="pill" href="/condos/astor-place-wheeling">Astor Place · $230K–$400K</a>
          <a class="pill" href="/condos/wolf-crossing-wheeling">Wolf Crossing · $380K–$520K</a>
          <a class="pill" href="/condos/millbrook-pointe-wheeling">Millbrook Pointe · $360K–$480K</a>
        </div>
      </details>
      <details class="city rv">
        <summary><span>Buffalo Grove <em class="city-count">4 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/the-wheatlands-buffalo-grove">The Wheatlands · $220K–$400K</a>
          <a class="pill" href="/condos/cambridge-on-the-lake-buffalo-grove">Cambridge on the Lake · $160K–$280K</a>
          <a class="pill" href="/condos/town-place-buffalo-grove">Town Place · $220K–$360K</a>
          <a class="pill" href="/condos/delacourte-condominiums-buffalo-grove">Delacourte · $240K–$380K</a>
        </div>
      </details>
      <details class="city rv">
        <summary><span>Des Plaines <em class="city-count">3 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/lexington-pointe-des-plaines">Lexington Pointe · $230K–$390K</a>
          <a class="pill" href="/condos/buckingham-place-des-plaines">Buckingham Place · $280K–$420K</a>
          <a class="pill" href="/condos/the-james-at-the-landings-des-plaines">The James at The Landings · $310K–$460K</a>
        </div>
      </details>
      <details class="city rv">
        <summary><span>Prospect Heights <em class="city-count">5 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/rob-roy-country-club-village">Rob Roy Country Club Village · $180K–$380K</a>
          <a class="pill" href="/condos/quincy-park-prospect-heights">Quincy Park · $195K–$250K</a>
          <a class="pill" href="/condos/willow-heights-prospect-heights">Willow Heights · $145K–$200K</a>
          <a class="pill" href="/condos/lake-run-prospect-heights">Lake Run · $175K–$200K</a>
          <a class="pill" href="/condos/willow-woods-prospect-heights">Willow Woods · $150K–$210K</a>
        </div>
      </details>
    </div>
  </div>
</section>

<!-- Search -->
<section class="section" id="search">
  <div class="wrap">
    <div class="sec-head rv">
      <p class="eyebrow">Find your home</p>
      <h2 class="h2">Search homes on the MLS.</h2>
      <p class="lead">Active listings across Prospect Heights, Mount Prospect, Arlington Heights, and beyond — updated daily.</p>
    </div>
    <form class="search-card rv" aria-label="Home search" action="https://search.dawnsellshomes.com/idx/results/listings" method="get" onsubmit="return dshSetCity()"><input type="hidden" name="pt" value="1"><input type="hidden" name="ccz" value="city"><input type="hidden" name="widgetReferer" value="true"><input type="hidden" name="city[]" id="dshCityId" value="">
      <div class="field field--loc"><label for="s-loc">City or area</label><input id="s-loc" type="text" list="dshCityList" placeholder="e.g. Mount Prospect" autocomplete="off"><datalist id="dshCityList"><option value="Addison"></option><option value="Algonquin"></option><option value="Antioch"></option><option value="Arlington Heights"></option><option value="Barrington"></option><option value="Barrington Hills"></option><option value="Bartlett"></option><option value="Beach Park"></option><option value="Bellwood"></option><option value="Bensenville"></option><option value="Bloomingdale"></option><option value="Bridgeport"></option><option value="Buffalo Grove"></option><option value="Carol Stream"></option><option value="Carpentersville"></option><option value="Cary"></option><option value="Chicago"></option><option value="Clarendon Hills"></option><option value="Crystal Lake"></option><option value="Deerfield"></option><option value="Des Plaines"></option><option value="Downers Grove"></option><option value="East Dundee"></option><option value="Elgin"></option><option value="Elk Grove Village"></option><option value="Elmhurst"></option><option value="Elmwood Park"></option><option value="Evanston"></option><option value="Fox Lake"></option><option value="Fox River Grove"></option><option value="Franklin Park"></option><option value="Glen Ellyn"></option><option value="Glencoe"></option><option value="Glendale Heights"></option><option value="Glenview"></option><option value="Grayslake"></option><option value="Gurnee"></option><option value="Hanover Park"></option><option value="Harwood Heights"></option><option value="Highland Park"></option><option value="Highwood"></option><option value="Hillside"></option><option value="Hinsdale"></option><option value="Hoffman Estates"></option><option value="Ingleside"></option><option value="Inverness"></option><option value="Island Lake"></option><option value="Itasca"></option><option value="La Grange Park"></option><option value="Lake Barrington"></option><option value="Lake Bluff"></option><option value="Lake Forest"></option><option value="Lake Villa"></option><option value="Lake in the Hills"></option><option value="Libertyville"></option><option value="Lincolnwood"></option><option value="Lindenhurst"></option><option value="Lisle"></option><option value="Lombard"></option><option value="Melrose Park"></option><option value="Morton Grove"></option><option value="Mount Prospect"></option><option value="Mundelein"></option><option value="Niles"></option><option value="Norridge"></option><option value="North Barrington"></option><option value="North Chicago"></option><option value="Northbrook"></option><option value="Northfield"></option><option value="Norwood Park"></option><option value="Oak Brook"></option><option value="Oakbrook Terrace"></option><option value="Palatine"></option><option value="Park Ridge"></option><option value="Prospect Heights"></option><option value="River Grove"></option><option value="Rolling Meadows"></option><option value="Roselle"></option><option value="Rosemont"></option><option value="Round Lake"></option><option value="Round Lake Beach"></option><option value="Round Lake Heights"></option><option value="Round Lake Park"></option><option value="Saint Charles"></option><option value="Schaumburg"></option><option value="Skokie"></option><option value="South Barrington"></option><option value="South Elgin"></option><option value="Streamwood"></option><option value="Third Lake"></option><option value="Vernon Hills"></option><option value="Villa Park"></option><option value="Volo"></option><option value="Wauconda"></option><option value="Waukegan"></option><option value="Wayne"></option><option value="West Dundee"></option><option value="Westchester"></option><option value="Western Springs"></option><option value="Westmont"></option><option value="Wheaton"></option><option value="Wheeling"></option><option value="Winnetka"></option><option value="Winthrop Harbor"></option><option value="Wood Dale"></option><option value="Zion"></option></datalist></div>
      <div class="field"><label for="s-min">Min price</label><select id="s-min" name="lp"><option value="">No min</option><option value="100000">$100K</option><option value="200000">$200K</option><option value="300000">$300K</option><option value="400000">$400K</option><option value="500000">$500K</option><option value="600000">$600K</option><option value="750000">$750K</option><option value="1000000">$1M</option></select></div>
      <div class="field"><label for="s-max">Max price</label><select id="s-max" name="hp"><option value="">No max</option><option value="300000">$300K</option><option value="400000">$400K</option><option value="500000">$500K</option><option value="600000">$600K</option><option value="750000">$750K</option><option value="1000000">$1M</option><option value="2000000">$2M</option></select></div>
      <div class="field"><label for="s-bed">Beds</label><select id="s-bed"><option>Any</option><option>1+</option><option>2+</option><option>3+</option><option>4+</option><option>5+</option></select></div>
      <div class="field"><label for="s-bath">Baths</label><select id="s-bath"><option>Any</option><option>1+</option><option>2+</option><option>3+</option><option>4+</option></select></div>
      <button class="btn btn--primary" type="submit">Search homes</button>
    </form>
  </div>
</section>

<!-- Team -->
<section class="section section--tight" id="team" style="padding-top:0">
  <div class="wrap">
    <div class="sec-head rv">
      <p class="eyebrow">The family behind the team</p>
      <h2 class="h2">Born and raised here. Still here.</h2>
      <p class="lead">The Dawn Simmons Team isn’t a marketing name — it’s literally a mother and son who grew up in these neighborhoods and now help other families put down roots of their own.</p>
    </div>
    <div class="team-grid">
      <div class="member rv">
        <div class="ph noimg"><div class="ph-frame">
          <img src="/images/dawn-simmons.jpg" alt="Dawn Simmons, REALTOR and Broker at RE/MAX Suburban" loading="lazy" onerror="this.closest(&#39;.ph&#39;).classList.add(&#39;noimg&#39;)">
          <div class="ph-fallback"><span class="mono">D</span><small>Dawn Simmons</small></div>
        </div></div>
        <div>
          <h3>Dawn Simmons</h3>
          <p class="role">REALTOR® · Broker · RE/MAX Hall of Fame</p>
          <p>Moved to Prospect Heights in 1988, raised three boys here, and has been selling homes in the northwest suburbs since 2001 — {{ \App\Support\TeamStats::soldTotal() }} transactions and counting. All three sons still call Prospect Heights home.</p>
          <a class="link-arrow" href="/team#dawn">Read Dawn’s story →</a>
        </div>
      </div>
      <div class="member rv">
        <div class="ph noimg"><div class="ph-frame">
          <img src="/images/josh-simmons.jpg" alt="Josh Simmons, Broker Associate at RE/MAX Suburban" loading="lazy" onerror="this.closest(&#39;.ph&#39;).classList.add(&#39;noimg&#39;)">
          <div class="ph-fallback"><span class="mono">J</span><small>Josh Simmons</small></div>
        </div></div>
        <div>
          <h3>Josh Simmons</h3>
          <p class="role">REALTOR® · Broker Associate</p>
          <p>The middle of Dawn’s three boys, a DePaul grad, and full-time on the team since college. Brings the energy, the hustle, and the local knowledge that only comes from growing up here.</p>
          <a class="link-arrow" href="/team#josh">Read Josh’s story →</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Reviews -->
<section class="section section--mist" id="reviews">
  <div class="wrap">
    <div class="rev-head rv">
      <p class="eyebrow" style="justify-content:center">Client reviews</p>
      <div class="rev-score">4.9</div>
      <div class="rev-stars-lg" aria-hidden="true">★★★★★</div>
      <p class="lead" style="margin:0 auto">Across 62+ Google reviews. Don’t just take our word for it.</p>
    </div>
    <div class="rev-grid">
      <div class="rev-card rv">
        <div class="rev-stars" aria-hidden="true">★★★★★</div>
        <p class="rev-quote">“Incredibly responsive, transparent, and always quick to answer questions. Made our first home purchase experience 11/10!”</p>
        <div class="rev-name">Mark Kegermann</div>
        <div class="rev-role">First-time buyer · Google review</div>
      </div>
      <div class="rev-card rv">
        <div class="rev-stars" aria-hidden="true">★★★★★</div>
        <p class="rev-quote">“Dawn is the best Realtor out there. She is kind, efficient, hard working — and my house sold in 2 days.”</p>
        <div class="rev-name">Charles Boyle</div>
        <div class="rev-role">Seller · Google review</div>
      </div>
      <div class="rev-card rv">
        <div class="rev-stars" aria-hidden="true">★★★★★</div>
        <p class="rev-quote">“Dawn did us good again. She is the absolute best. Also Josh is super — he learned well from Mom.”</p>
        <div class="rev-name">Kurt Koziol</div>
        <div class="rev-role">Repeat client · Google review</div>
      </div>
    </div>
    <p style="margin-top:2rem" class="rv"><a class="link-arrow" href="/reviews">Read all client reviews →</a></p>
  </div>
</section>

<!-- Blog -->
<section class="section" id="blog">
  <div class="wrap">
    <div class="sec-head rv">
      <p class="eyebrow">From the blog</p>
      <h2 class="h2">Local insights, straight talk.</h2>
    </div>
    <div class="blog-grid">
      <a class="blog-card rv" href="/blog/2026-08-northwest-suburbs-market-update">
        <span class="blog-cat">Market update</span>
        <h3>Northwest Suburbs Market Update — August 2026</h3>
        <p>Palatine’s single-family median jumps 16.4% to $600K, Arlington Heights runs $315K to $2.65M, and Wheeling’s dozen-home squeeze grinds on.</p>
        <span class="link-arrow">Read the update →</span>
      </a>
      <a class="blog-card rv" href="/blog/prospect-heights-il-living-guide">
        <span class="blog-cat">Neighborhood guide</span>
        <h3>Is Prospect Heights a Good Place to Live?</h3>
        <p>A local’s honest guide from a family that’s been here since 1988.</p>
        <span class="link-arrow">Read the guide →</span>
      </a>
      <a class="blog-card rv" href="/blog/first-time-homebuyer-guide-northwest-suburbs">
        <span class="blog-cat">First-time buyers</span>
        <h3>First-Time Homebuyer Guide to the NW Suburbs</h3>
        <p>The practical advice we give every first-time buyer, start to finish.</p>
        <span class="link-arrow">Read the guide →</span>
      </a>
    </div>
  </div>
</section>

<!-- Video band -->
<section class="section section--ink video-band" id="videos">
  <div class="wrap">
    <div class="rv">
      <p class="eyebrow">Video walkthroughs</p>
      <h2 class="h2" style="color:#fff">See homes before you visit.</h2>
      <p class="lead" style="margin-top:.9rem">Real neighborhood and home tours from Josh, shot on location — so you can get a feel for a space before you ever step inside.</p>
    </div>
    <div class="views rv">
      <div><div class="stat-num">10M+</div><div class="stat-label">views on a single home tour</div></div>
      <a class="btn btn--light" href="https://www.instagram.com/joshsimmonsre/">Follow @joshsimmonsre →</a>
    </div>
  </div>
</section>

<!-- Property management -->
<section class="section section--tight">
  <div class="wrap">
    <div class="pm rv">
      <div>
        <h3>Own a rental? We’ll manage it for you.</h3>
        <p>Tenant screening, rent collection, and maintenance — handled start to finish. Flat-rate pricing from $100/month.</p>
      </div>
      <div class="pm-actions">
        <a class="btn btn--primary" href="/property-management">Learn about property management</a>
        <a class="btn btn--ghost" href="tel:8477381884">Call (847) 738-1884</a>
      </div>
    </div>
  </div>
</section>

<!-- Contact -->
<section class="section section--mist" id="contact">
  <div class="wrap">
    <div class="contact-grid">
      <div class="rv">
        <p class="eyebrow">Get in touch</p>
        <h2 class="h2">Let’s talk about your next move.</h2>
        <p class="lead" style="margin-top:.9rem">Buying, selling, or just exploring your options — reach out and we’ll get back to you within 24 hours.</p>
        <div class="direct">
          <div class="direct-item">
            <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h3l1.5 4L7.5 9.5a11 11 0 0 0 5 5L14 12.5 18 14v3a2 2 0 0 1-2 2A13 13 0 0 1 3 6a2 2 0 0 1 2-2Z"></path></svg></div>
            <div><strong>Dawn Simmons</strong><a href="tel:8477381884">(847) 738-1884</a><br><span>REALTOR® · Broker · RE/MAX Suburban</span></div>
          </div>
          <div class="direct-item">
            <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h10a1.5 1.5 0 0 1 1.5 1.5v13A1.5 1.5 0 0 1 17 20H7a1.5 1.5 0 0 1-1.5-1.5v-13A1.5 1.5 0 0 1 7 4Z"></path><path d="M10.5 17h3"></path></svg></div>
            <div><strong>Josh Simmons</strong><a href="tel:2246284013">(224) 628-4013</a><br><span>REALTOR® · Broker Associate</span></div>
          </div>
          <div class="direct-item">
            <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="5.5" width="17" height="13" rx="2"></rect><path d="m4.5 7 7.5 5.5L19.5 7"></path></svg></div>
            <div><strong>Email</strong><a href="mailto:dawn@dawnsellshomes.com">dawn@dawnsellshomes.com</a></div>
          </div>
        </div>
        <p class="direct-note">Those are our personal cells — call or text any day, 7 days a week.</p>
      </div>
      <div class="form-card rv" id="formCard">
        <form id="contactForm" x-data="contactForm()" @submit.prevent="send()">
          <div class="form-grid">
            <div class="field"><label for="f-name">Full name *</label><input id="f-name" name="name" type="text" required x-model="f.name"></div>
            <div class="field"><label for="f-email">Email address *</label><input id="f-email" name="email" type="email" required x-model="f.email"></div>
            <div class="field"><label for="f-phone">Phone number</label><input id="f-phone" name="phone" type="tel" x-model="f.phone"></div>
            <div class="field"><label for="f-goal">I am looking to</label><select id="f-goal" name="interest" x-model="f.interest"><option value="">Select one…</option><option value="buy">Buy a home</option><option value="sell">Sell my home</option><option value="both">Buy &amp; sell</option><option value="value">Get a home valuation</option><option value="invest">Invest</option><option value="rent">Rent</option><option value="other">Something else</option></select></div>
            <div class="field full"><label for="f-msg">Message</label><textarea id="f-msg" name="message" rows="4" x-model="f.message" placeholder="Tell us a little about your plans…"></textarea></div>
          </div>
          <div style="margin-top:1.15rem"><p class="hp-field" style="position:absolute;left:-9999px" aria-hidden="true"><label>Don't fill this out: <input name="bot-field" x-model="f.bot" tabindex="-1"></label></p>
<button class="btn btn--primary" type="submit" x-text="busy ? 'Sending…' : 'Send message'" :disabled="busy">Send message</button>
<p x-show="sent" x-cloak class="form-ok">✓ Message sent — we'll get back to you within 24 hours. Talk soon!</p></div>
          <p class="form-note">By submitting this form you agree to be contacted by The Dawn Simmons Team. We never share your information.</p>
        </form>
        <div class="success" role="status">
          <div class="mark" aria-hidden="true"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></div>
          <h3>Message received!</h3>
          <p>Thanks for reaching out. Dawn or Josh will be in touch within 24 hours.</p>
        </div>
      </div>
    </div>
  </div>
</section>

</main>

<!-- Footer -->
<footer class="footer">
  <div class="wrap">
    <div class="foot-grid">
      <div class="foot-brand">
        <span class="brand-name">DAWN SIMMONS TEAM</span><br>
        <span class="brand-sub" style="color:var(--red-soft)">RE/MAX Suburban</span>
        <p>Serving Chicago’s northwest suburbs since 2001. Another DAWN Deal.</p>
        <div class="socials">
          <a href="https://www.instagram.com/joshsimmonsre/">Instagram</a>
          <a href="https://www.facebook.com/joshua.simmons">Facebook</a>
        </div>
      </div>
      <div>
        <h4>Explore</h4>
        <ul>
          <li><a href="#search">Browse homes</a></li>
          <li><a href="#areas">Neighborhoods</a></li>
          <li><a href="#condos">Condos &amp; townhomes</a></li>
          <li><a href="/team">Our team</a></li>
          <li><a href="/reviews">Reviews</a></li>
          <li><a href="/blog">Blog</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </div>
      <div>
        <h4>Tools &amp; guides</h4>
        <ul>
          <li><a href="/moving-to-northwest-suburbs">Moving guide: start here</a></li>
          <li><a href="/chain-o-lakes">Chain O’Lakes guide</a></li>
          <li><a href="/off-market-homes">Off-market homes</a></li>
          <li><a href="/mortgage-calculator">Mortgage calculator</a></li>
          <li><a href="/seller-net-sheet">Seller net sheet</a></li>
          <li><a href="/property-management">Property management</a></li>
          <li><a href="/chicago-suburban-real-estate-group">Investor meetup</a></li>
        </ul>
      </div>
      <div>
        <h4>RE/MAX Suburban offices</h4>
        <p class="offices">
          <a href="https://maps.google.com/maps?q=330%20E%20Northwest%20Hwy%2C%20Mount%20Prospect%2C%20IL%2060056">Mount Prospect</a> · <a href="https://maps.google.com/maps?q=1344%20S%20Milwaukee%20Ave%2C%20Libertyville%2C%20IL%2060048">Libertyville</a> · <a href="https://maps.google.com/maps?q=1808%20N%20Arlington%20Heights%20Rd%2C%20Arlington%20Heights%2C%20IL%2060004">Arlington Heights</a> · <a href="https://maps.google.com/maps?q=1125%20Weiland%20Rd%2C%20Buffalo%20Grove%2C%20IL%2060089">Buffalo Grove</a> · <a href="https://maps.google.com/maps?q=1310%20N%20Roselle%20Rd%2C%20Schaumburg%2C%20IL%2060195">Schaumburg North</a> · <a href="https://maps.google.com/maps?q=2311%20W%20Schaumburg%20Rd%2C%20Schaumburg%2C%20IL%2060194">Schaumburg West</a> · <a href="https://maps.google.com/maps?q=444%20S%20Rand%20Rd%2C%20Lake%20Zurich%2C%20IL%2060047">Lake Zurich</a> · <a href="https://maps.google.com/maps?q=7107%20Pingree%20Rd%2C%20Crystal%20Lake%2C%20IL%2060014">Crystal Lake</a> · <a href="https://maps.google.com/maps?q=2405%20Harnish%20Dr%2C%20Algonquin%2C%20IL%2060102">Algonquin</a> · <a href="https://maps.google.com/maps?q=441%20Taft%20Ave%2C%20Glen%20Ellyn%2C%20IL%2060137">Glen Ellyn</a> · <a href="https://maps.google.com/maps?q=1417%20N%20Main%20St%2C%20Wheaton%2C%20IL%2060187">Wheaton</a>
        </p>
      </div>
    </div>
    <div class="foot-bottom">
      <span>© 2026 The Dawn Simmons Team · RE/MAX Suburban · Licensed in Illinois</span>
      <span>Information deemed reliable but not guaranteed. Equal Housing Opportunity.</span>
    </div>
  </div>
</footer>

<!-- Mobile call bar -->
<div class="mob-cta" aria-label="Quick contact">
  <a class="btn btn--primary" href="tel:8477381884">Call now</a>
  <a class="btn btn--ghost" href="sms:2246284013?&amp;body=Hi%20Josh%20%E2%80%94%20I%27m%20on%20dawnsellshomes.com%20and%20have%20a%20question%20about%20">Text Josh</a>
</div>

<script>
// Mobile menu
var nav = document.getElementById('nav');
var menuBtn = document.getElementById('menuBtn');
menuBtn.addEventListener('click', function () {
  var open = nav.classList.toggle('open');
  menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
});
document.querySelectorAll('#navLinks a').forEach(function (a) {
  a.addEventListener('click', function () {
    nav.classList.remove('open');
    menuBtn.setAttribute('aria-expanded', 'false');
  });
});

// Area tabs
var tabs = document.querySelectorAll('.tab');
tabs.forEach(function (tab) {
  tab.addEventListener('click', function () {
    tabs.forEach(function (t) {
      t.setAttribute('aria-selected', 'false');
      document.getElementById(t.getAttribute('aria-controls')).hidden = true;
    });
    tab.setAttribute('aria-selected', 'true');
    document.getElementById(tab.getAttribute('aria-controls')).hidden = false;
  });
});

// Scroll reveal
if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
    });
  }, { threshold: 0.12 });
  document.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
} else {
  document.querySelectorAll('.rv').forEach(function (el) { el.classList.add('in'); });
}

// Hero valuation: route to the contact form with the address pre-filled
document.getElementById('valForm').addEventListener('submit', function (e) {
  e.preventDefault();
  var addr = document.getElementById('valAddress').value.trim();
  var goal = document.getElementById('f-goal');
  var msg = document.getElementById('f-msg');
  goal.value = 'Get a home valuation';
  if (addr) msg.value = 'Home valuation request for: ' + addr;
  document.getElementById('contact').scrollIntoView({ behavior: 'smooth' });
  setTimeout(function () { document.getElementById('f-name').focus({ preventScroll: true }); }, 600);
});

// Contact form (demo submit)
document.getElementById('contactForm').addEventListener('submit', function (e) {
  e.preventDefault();
  document.getElementById('formCard').classList.add('done');
});

// MLS search (demo — wires to IDX feed on the live site)
document.querySelector('.search-card').addEventListener('submit', function (e) {
  e.preventDefault();
  window.location.href = '/#search';
});
</script>



@livewireScripts
@include('components.home.value-logic')
<script>
// contact form (Alpine): posts the same contract the live pipeline accepts
document.addEventListener('alpine:init', () => {
  Alpine.data('contactForm', () => ({
    f: { name:'', email:'', phone:'', interest:'', message:'', bot:'' }, busy:false, sent:false,
    async send() {
      this.busy = true;
      const d = new URLSearchParams({ 'form-name':'contact', ...this.f, 'bot-field': this.f.bot });
      try { await fetch('/', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'}, body:d.toString() }); } catch(e) {}
      this.busy = false; this.sent = true; this.f = { name:'', email:'', phone:'', interest:'', message:'', bot:'' };
    },
  }));
});
// heroSearch(): the shared valuation CTA — prefill + jump to contact
function heroSearch() {
  var el = document.getElementById('valAddress');
  var a = el ? el.value.trim() : '';
  var root = document.getElementById('contactForm');
  if (root && window.Alpine) {
    var c = Alpine.$data(root);
    c.f.interest = 'value';
    c.f.message = a ? "I'd like a free home valuation for: " + a : "I'd like a free home valuation.";
  }
  location.hash = '#contact';
  setTimeout(function() { var n = document.getElementById('f-name'); if (n) n.focus(); }, 400);
}
// ?val= and ?pln= prefills (same contract as the live homepage)
(function() {
  var q = new URLSearchParams(location.search);
  var val = q.get('val'), pln = q.get('pln');
  if (!val && !pln) return;
  document.addEventListener('alpine:initialized', function() {
    var root = document.getElementById('contactForm'); if (!root) return;
    var c = Alpine.$data(root);
    if (val !== null) { c.f.interest = 'value'; c.f.message = val ? "I'd like a free home valuation for my home in " + val + "." : "I'd like a free home valuation."; }
    if (pln !== null) { c.f.interest = 'buy'; c.f.message = "I'm looking to buy in " + pln + " and want to hear about Private Listing Network / off-market matches. Here's what I'm looking for: "; }
    location.hash = '#contact';
  });
})();
// IDX search city mapping (same data the live homepage uses)
var DSH_CITIES={"addison": "253", "algonquin": "620", "antioch": "1327", "arlington heights": "1615", "barrington": "2676", "barrington hills": "2680", "bartlett": "2710", "beach park": "3046", "bellwood": "3493", "bensenville": "3640", "bloomingdale": "4485", "bridgeport": "5557", "buffalo grove": "6258", "carol stream": "7386", "carpentersville": "7409", "cary": "7526", "chicago": "8569", "clarendon hills": "8901", "crystal lake": "11067", "deerfield": "11829", "des plaines": "12146", "downers grove": "12685", "east dundee": "13345", "elgin": "14188", "elk grove village": "14259", "elmhurst": "14456", "elmwood park": "14496", "evanston": "15111", "fox lake": "16798", "fox river grove": "16803", "franklin park": "16911", "glen ellyn": "18068", "glencoe": "18123", "glendale heights": "18152", "glenview": "18212", "grayslake": "18923", "gurnee": "19525", "hanover park": "19994", "harwood heights": "20371", "highland park": "21175", "highwood": "21206", "hillside": "21314", "hinsdale": "21376", "hoffman estates": "21472", "ingleside": "22601", "inverness": "22662", "island lake": "22829", "itasca": "22864", "la grange park": "24765", "lake barrington": "25001", "lake bluff": "25004", "lake forest": "25070", "lake villa": "25241", "lake in the hills": "25106", "libertyville": "26382", "lincolnwood": "26540", "lindenhurst": "26565", "lisle": "26667", "lombard": "27031", "melrose park": "29636", "morton grove": "31301", "mount prospect": "31561", "mundelein": "31798", "niles": "32998", "norridge": "33175", "north barrington": "33214", "north chicago": "33276", "northbrook": "33610", "northfield": "33631", "norwood park": "33753", "oak brook": "33853", "oakbrook terrace": "33925", "palatine": "35177", "park ridge": "35511", "prospect heights": "37959", "river grove": "39525", "rolling meadows": "40040", "roselle": "40212", "rosemont": "40218", "round lake": "40331", "round lake beach": "40335", "round lake heights": "40336", "round lake park": "40337", "saint charles": "40809", "schaumburg": "41730", "skokie": "43130", "south barrington": "43554", "south elgin": "43645", "streamwood": "45006", "third lake": "46202", "vernon hills": "48964", "villa park": "49229", "volo": "49478", "wauconda": "50253", "waukegan": "50258", "wayne": "50334", "west dundee": "50795", "westchester": "51184", "western springs": "51197", "westmont": "51261", "wheaton": "51426", "wheeling": "51447", "winnetka": "52260", "winthrop harbor": "52332", "wood dale": "52455", "zion": "53088"};
function dshSetCity() {
  var t = (document.getElementById('s-loc').value || '').trim().toLowerCase();
  document.getElementById('dshCityId').value = DSH_CITIES[t] || '';
  return true;
}
</script>
</body>
</html>
