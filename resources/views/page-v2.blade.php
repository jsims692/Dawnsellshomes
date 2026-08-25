{{-- Imported legacy page rendered inside the v2 chrome: its baked-in nav,
     footer and floating widget are stripped at render (PageController) and
     the shared header/footer/plat background take over. The compat rules
     below neutralize legacy element-level nav/footer CSS that would
     otherwise bleed into the v2 components. --}}
<x-site.layout :page="$page" :head="$head">
<style>
  .header .nav { background:transparent !important; height:auto !important; position:relative !important; box-shadow:none !important; padding:.85rem 0 !important; }
  .header .nav-links a { color:var(--slate) !important; text-transform:none !important; letter-spacing:0 !important; font-size:.93rem !important; margin-left:0 !important; }
  .header .nav-links a:hover { color:var(--ink) !important; }
  .header .nav-links a.btn { color:#fff !important; }
  .footer { text-align:left !important; margin-top:0 !important; }
  .footer a { color:inherit !important; }
  .footer p { color:#AFC0D1 !important; }
  .footer h4 { color:#fff !important; }
</style>
{!! $body !!}
</x-site.layout>
