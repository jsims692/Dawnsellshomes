<div style="font-family: Arial, sans-serif; max-width: 560px; color: #222;">
    <h2 style="color: #1B3A6B; border-bottom: 3px solid #CC0000; padding-bottom: 8px;">New Lead from dawnsellshomes.com</h2>
    <table cellpadding="6" style="font-size: 15px; line-height: 1.5;">
        <tr><td style="color:#666;">Name</td><td><strong>{{ $lead->name ?: '—' }}</strong></td></tr>
        <tr><td style="color:#666;">Email</td><td><a href="mailto:{{ $lead->email }}">{{ $lead->email ?: '—' }}</a></td></tr>
        <tr><td style="color:#666;">Phone</td><td><a href="tel:{{ preg_replace('/\D+/', '', $lead->phone) }}">{{ $lead->phone ?: '—' }}</a></td></tr>
        <tr><td style="color:#666;">Interest</td><td>{{ $lead->interest ?: '—' }}</td></tr>
        <tr><td style="color:#666;">Submitted from</td><td>{{ $lead->source_page ?: '—' }}</td></tr>
        <tr><td style="color:#666;">Received</td><td>{{ $lead->created_at->timezone('America/Chicago')->format('M j, Y g:i A') }} CT</td></tr>
    </table>
    <div style="background:#F8F6F2; border-left: 4px solid #C8A84B; padding: 12px 16px; margin-top: 12px; white-space: pre-wrap;">{{ $lead->message ?: '(no message)' }}</div>
    <p style="color:#999; font-size: 12px; margin-top: 18px;">Also delivered to kvCORE{{ $lead->forwarded_at ? ' ✓' : ' (delivery pending — check the site leads table)' }}. Reply directly to this email to answer the lead only if your mail app supports reply-to; otherwise use the contact info above.</p>
</div>
