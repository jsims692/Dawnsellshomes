<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'media' => 'array',
            'address_public' => 'boolean',
            'display_public' => 'boolean',
            'avm_allowed' => 'boolean',
            'comments_allowed' => 'boolean',
            'is_demo' => 'boolean',
            'mls_modified_at' => 'datetime',
            'close_date' => 'date',
            'listing_contract_date' => 'date',
            'new_construction' => 'boolean',
            'waterfront' => 'boolean',
        ];
    }

    /** Room-by-room detail (name, dimensions, level, flooring). */
    public function rooms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ListingRoom::class)->orderBy('sort');
    }

    /** Multi-value attributes (appliances, features, amenities…), one row per item. */
    public function features(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ListingFeature::class);
    }

    /** Feature values for one category, comma-joined for display. */
    public function featureList(string $category): ?string
    {
        $values = $this->features->where('category', $category)->pluck('value');

        return $values->isEmpty() ? null : $values->implode(', ');
    }

    /** Only listings the rules allow us to display. */
    public function scopeDisplayable(Builder $q): Builder
    {
        return $q->where('display_public', true);
    }

    /** On-market listings (the table also holds recently-Closed rows for stats). */
    public function scopeForSale(Builder $q): Builder
    {
        return $q->whereIn('status', ['Active', 'Active Under Contract']);
    }

    /**
     * Locally cached primary photo (mls:media). MLS GRID media URLs are signed,
     * expire within the hour, and rate-limit hotlinking — never link them directly.
     */
    /** Team agent ids from config, digits-only (feed uses "MRD85211"). */
    public static function teamIds(): array
    {
        return array_map(fn ($id) => preg_replace('/\D/', '', (string) $id), config('site.team_agent_ids', []));
    }

    /** 'listing', 'buyer', or null — which side of this deal was the team's. */
    public function teamSide(): ?string
    {
        $ids = self::teamIds();
        $n = fn ($v) => preg_replace('/\D/', '', (string) $v);
        if (in_array($n($this->list_agent_id), $ids, true) || in_array($n($this->colist_agent_id), $ids, true)) {
            return 'listing';
        }

        return in_array($n($this->buyer_agent_id), $ids, true) ? 'buyer' : null;
    }

    public function isForSale(): bool
    {
        return in_array($this->status, ['Active', 'Active Under Contract'], true);
    }

    /** All locally cached photos in gallery order — tolerant of gaps left by
     *  individual failed downloads ({key}.jpg, {key}-1.jpg, {key}-3.jpg …). */
    public function photoUrls(): array
    {
        $base = storage_path('app/public/listings/');
        $files = array_merge(
            is_file($base.$this->listing_key.'.jpg') ? [$base.$this->listing_key.'.jpg'] : [],
            glob($base.$this->listing_key.'-*.jpg') ?: [],
        );
        usort($files, function ($a, $b) {
            $n = fn ($f) => (int) (preg_match('/-(\d+)\.jpg$/', $f, $m) ? $m[1] : 0);

            return $n($a) <=> $n($b);
        });

        return array_map(fn ($f) => asset('storage/listings/'.basename($f)), $files);
    }

    public function photoUrl(): ?string
    {
        $rel = 'listings/'.$this->listing_key.'.jpg';

        return file_exists(storage_path('app/public/'.$rel)) ? asset('storage/'.$rel) : null;
    }

    public function baths(): string
    {
        $full = (int) $this->baths_full;
        $half = (int) $this->baths_half;

        return $half > 0 ? "{$full}.5" : (string) $full;
    }

    /** Address line honoring the seller's internet-display election (Rule 7). */
    public function displayAddress(): string
    {
        return $this->address_public && $this->street_address
            ? "{$this->street_address}, {$this->city}, {$this->state} {$this->zip}"
            : "Address not displayed at seller's request — {$this->city}, {$this->state}";
    }
}
