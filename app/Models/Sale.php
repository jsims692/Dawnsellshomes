<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        // Any change to a sale invalidates the cached map payload.
        static::saved(fn () => cache()->forget('sales-map-payload'));
        static::deleted(fn () => cache()->forget('sales-map-payload'));
    }

    protected function casts(): array
    {
        return [
            'sold_at' => 'date',
            'lat' => 'float',
            'lng' => 'float',
            'is_public' => 'boolean',
        ];
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeMapped(Builder $query): Builder
    {
        return $query->whereNotNull('lat')->whereNotNull('lng');
    }
}
