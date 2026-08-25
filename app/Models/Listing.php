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
    public function isForSale(): bool
    {
        return in_array($this->status, ['Active', 'Active Under Contract'], true);
    }

    /** All locally cached photos, in gallery order ({key}.jpg, {key}-1.jpg …). */
    public function photoUrls(): array
    {
        $base = storage_path('app/public/listings/');
        $urls = [];
        if (is_file($base.$this->listing_key.'.jpg')) {
            $urls[] = asset('storage/listings/'.$this->listing_key.'.jpg');
            for ($i = 1; is_file($base.$this->listing_key.'-'.$i.'.jpg'); $i++) {
                $urls[] = asset('storage/listings/'.$this->listing_key.'-'.$i.'.jpg');
            }
        }

        return $urls;
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
