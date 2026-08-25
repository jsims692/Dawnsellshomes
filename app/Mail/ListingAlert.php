<?php

namespace App\Mail;

use App\Models\SavedSearch;
use Illuminate\Mail\Mailable;

class ListingAlert extends Mailable
{
    public function __construct(
        public SavedSearch $search,
        public $listings,
    ) {}

    public function build()
    {
        $n = $this->listings->count();

        return $this->subject($n.' new '.($n === 1 ? 'home matches' : 'homes match').' your search — '.$this->search->summary())
            ->view('emails.listing-alert');
    }
}
