<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead)
    {
    }

    public function envelope(): Envelope
    {
        $who = trim($this->lead->name) ?: ($this->lead->email ?: 'Unknown');
        $interest = $this->lead->interest ? " ({$this->lead->interest})" : '';

        return new Envelope(
            subject: "NEW LEAD: {$who}{$interest} — dawnsellshomes.com",
            replyTo: filter_var($this->lead->email, FILTER_VALIDATE_EMAIL)
                ? [new \Illuminate\Mail\Mailables\Address($this->lead->email, $this->lead->name ?: $this->lead->email)]
                : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-notification',
        );
    }
}
