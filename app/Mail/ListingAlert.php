<?php

namespace App\Mail;

use App\Models\SavedSearch;
use Illuminate\Mail\Mailable;

class ListingAlert extends Mailable
{
    public function __construct(
        public SavedSearch $search,
        public $listings,
        public $drops = null,
    ) {
        $this->drops = $drops ?? collect();
    }

    public function build()
    {
        $n = $this->listings->count();
        $d = $this->drops->count();
        $parts = array_filter([
            $n ? $n.' new '.($n === 1 ? 'home' : 'homes') : null,
            $d ? $d.' price '.($d === 1 ? 'drop' : 'drops') : null,
        ]);

        return $this->subject(implode(' + ', $parts).' — '.$this->search->summary())
            ->view('emails.listing-alert');
    }
}
