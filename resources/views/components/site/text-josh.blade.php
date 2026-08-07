{{-- Floating "Text Josh" pill: sms deep-link on mobile, popover with the number on desktop --}}
<div id="tj-w" style="position:fixed;bottom:18px;right:18px;z-index:9999;font-family:Arial,sans-serif;"
     x-data="{ open: false, mobile: /Android|iPhone|iPad|iPod/i.test(navigator.userAgent) }">
<div id="tj-card" x-show="open" style="display:none;background:#fff;border:1px solid #d0d7e8;border-radius:10px;box-shadow:0 8px 28px rgba(0,0,0,.18);padding:16px 18px;margin-bottom:10px;max-width:250px;font-size:14px;color:#222;line-height:1.6;">Questions? Text or call Josh directly:<br><strong style="font-size:16px;color:#1B3A6B;">(224) 628-4013</strong><br><a href="/#contact" style="color:#CC0000;font-weight:700;">or send a message &rarr;</a></div>
<a id="tj-btn" href="sms:2246284013?&amp;body=Hi%20Josh%20%E2%80%94%20I%27m%20on%20dawnsellshomes.com%20and%20have%20a%20question%20about%20"
   @click="if (!mobile) { $event.preventDefault(); open = !open }"
   style="display:inline-block;background:#CC0000;color:#fff;padding:13px 20px;border-radius:28px;font-family:Arial,sans-serif;font-weight:700;font-size:14px;text-decoration:none;box-shadow:0 4px 14px rgba(0,0,0,.25);">Text Josh</a>
</div>
