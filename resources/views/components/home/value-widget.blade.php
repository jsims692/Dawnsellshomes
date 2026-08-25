@php $gkey = config('services.google.maps_key'); @endphp
{{--
  Homepage "What is your home worth?" widget.
  1) Google Places autocomplete on the address field (Chicago-area biased).
  2) On select/submit: geocode -> /home-value/nearby -> show real nearby closed
     sales from the team's own record (median + range + sample streets).
  3) CTA hands off to the contact form with address + interest pre-filled
     (same heroSearch() contract the page already had).
  Degrades gracefully: without a Google key it behaves exactly like before.
--}}
<div class="value-widget" x-data="homeValue()" x-init="init()">
    <p>🏠 What is your home worth? Enter your address to find out.</p>
    <div class="value-row" style="position:relative;">
        <input type="text" id="heroAddrInput" x-ref="addr" x-model="query" @input.debounce.220ms="suggest()" @keydown.down.prevent="move(1)" @keydown.up.prevent="move(-1)" @keydown.enter.prevent="submit()" @keydown.escape="preds=[]" @focus="if(query.length>2) suggest()" placeholder="Enter your home address…" autocomplete="off" aria-autocomplete="list" aria-expanded="false" :aria-expanded="preds.length>0" role="combobox">
        <button @click="submit()" type="button" x-text="busy ? 'Checking…' : 'Get Value →'">Get Value →</button>
        <ul x-show="preds.length" x-cloak @mousedown.prevent class="hv-preds" role="listbox">
            <template x-for="(p,i) in preds" :key="p.id">
                <li :class="{active: i===hi}" @click="pick(p)" @mouseenter="hi=i" role="option">
                    <strong x-text="p.main"></strong> <span x-text="p.secondary"></span>
                </li>
            </template>
            <li class="hv-attrib">Powered by Google</li>
        </ul>
    </div>
    <p style="font-size:12px;color:rgba(255,255,255,.5);margin-top:8px;" x-show="!result">Or <a href="#search" style="color:var(--gold)">search available homes →</a></p>

    {{-- results --}}
    <div x-show="result" x-cloak x-transition class="hv-result">
        <template x-if="result && result.ok">
            <div>
                <div class="hv-kicker"><span x-text="result.kicker"></span> <span x-text="shortAddr"></span></div>
                <div class="hv-median" x-text="fmt(result.low)+' – '+fmt(result.high)"></div>
                <div class="hv-sub">What nearby homes actually sold for · median <strong x-text="fmt(result.median)"></strong></div>
                <div class="hv-sub" x-text="result.basis"></div>
                <div class="hv-sub" x-show="result.ours_line" x-text="result.ours_line" style="font-weight:600;"></div>
                <ul class="hv-sample">
                    <template x-for="s in result.sample" :key="s.address+s.year">
                        <li>
                            <a x-show="s.url" :href="s.url" target="_blank" rel="noopener" style="color:inherit;text-decoration:underline;text-underline-offset:2px;" x-text="s.address+', '+s.city"></a>
                            <span x-show="!s.url" x-text="s.address+', '+s.city"></span>
                            <span x-text="fmt(s.price)+' · '+(s.when || s.year)"></span>
                        </li>
                    </template>
                </ul>
                <p class="hv-note">That's the neighborhood — not your house. Your kitchen, your lot, your updates and today's buyer demand move the number a lot. We'll run a real, no-obligation valuation on your specific home &mdash; usually within 24 hours.</p>
                <p class="hv-attrib-line" x-show="result.attribution" x-text="result.attribution" style="font-size:10.5px;color:rgba(255,255,255,.55);margin:0 0 10px;line-height:1.5;"></p>
                <button type="button" class="hv-cta" @click="toContact()">Get My Exact Number — Free →</button>
            </div>
        </template>
        <template x-if="result && !result.ok">
            <div>
                <div class="hv-kicker">We haven't closed enough sales right around <span x-text="shortAddr"></span> to show a fair snapshot.</div>
                <p class="hv-note">That doesn't mean we don't know the area &mdash; it means the honest answer needs a real look. We'll pull the actual comps and send you a no-obligation valuation, usually within 24 hours.</p>
                <button type="button" class="hv-cta" @click="toContact()">Get My Free Valuation →</button>
            </div>
        </template>
    </div>
</div>

<style>
.hv-preds { position:absolute; left:0; right:0; top:100%; margin:6px 0 0; padding:6px 0; list-style:none; background:#fff; color:#222; border-radius:8px; box-shadow:0 12px 32px rgba(0,0,0,.28); z-index:50; text-align:left; font-family:Arial,sans-serif; font-size:14px; max-height:280px; overflow:auto; }
.hv-preds li { padding:10px 14px; cursor:pointer; line-height:1.35; }
.hv-preds li strong { color:#1B3A6B; }
.hv-preds li span { color:#666; font-size:12.5px; }
.hv-preds li.active, .hv-preds li:hover { background:#F0F2F5; }
.hv-preds li.hv-attrib { font-size:10px; color:#999; text-align:right; padding:4px 12px 0; cursor:default; }
.hv-preds li.hv-attrib:hover { background:transparent; }
.hv-result { margin-top:16px; padding-top:16px; border-top:1px solid rgba(255,255,255,.18); text-align:left; font-family:Arial,sans-serif; }
.hv-kicker { font-size:12px; letter-spacing:.6px; text-transform:uppercase; color:var(--gold); font-weight:700; }
.hv-median { font-size:29px; font-weight:800; color:#fff; line-height:1.1; margin:6px 0 4px; }
.hv-sub { font-size:13px; color:rgba(255,255,255,.8); }
.hv-sub strong { color:#fff; }
.hv-sample { list-style:none; margin:12px 0 0; padding:0; font-size:12.5px; color:rgba(255,255,255,.85); }
.hv-sample li { display:flex; justify-content:space-between; gap:12px; padding:5px 0; border-bottom:1px dashed rgba(255,255,255,.14); }
.hv-sample li span:last-child { color:#fff; font-weight:700; white-space:nowrap; }
.hv-note { font-size:13px; color:rgba(255,255,255,.78); margin:12px 0 12px; line-height:1.55; }
.hv-cta { background:var(--red); color:#fff; border:0; border-radius:6px; padding:13px 20px; font-family:Arial,sans-serif; font-weight:700; font-size:15px; cursor:pointer; width:100%; }
.hv-cta:hover { background:#a80000; }
</style>

@include('components.home.value-logic')
