<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'og_tags' => 'array',
            'json_ld' => 'array',
            'in_sitemap' => 'boolean',
        ];
    }

    public function cityPage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'city_page_id');
    }

    public function childPages(): HasMany
    {
        return $this->hasMany(Page::class, 'city_page_id');
    }

    public function style(): ?PageStyle
    {
        return $this->css_key ? PageStyle::where('key', $this->css_key)->first() : null;
    }
}
