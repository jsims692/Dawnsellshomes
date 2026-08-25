<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['criteria' => 'array', 'last_notified_at' => 'datetime', 'active' => 'boolean'];
    }

    /** Human-readable criteria summary ("Detached homes in Prospect Heights under $500K, 3+ beds"). */
    public function summary(): string
    {
        $c = $this->criteria;
        $type = ['detached' => 'Detached homes', 'attached' => 'Condos & townhomes', 'multi' => '2–4 unit buildings', 'multi5' => '5+ unit buildings'][$c['dwelling'] ?? ''] ?? 'Homes';
        $cityTxt = implode(' / ', array_filter((array) ($c['city'] ?? [])));
        $parts = [$type.($cityTxt !== '' ? ' in '.$cityTxt : ' anywhere we serve')];
        if ($c['min'] ?? null) {
            $parts[] = 'over $'.number_format($c['min']);
        }
        if ($c['max'] ?? null) {
            $parts[] = 'under $'.number_format($c['max']);
        }
        if ($c['beds'] ?? null) {
            $parts[] = $c['beds'].'+ beds';
        }

        return implode(', ', $parts);
    }

    /** For-sale listings matching this search. */
    public function matches()
    {
        $c = $this->criteria;

        return Listing::displayable()->forSale()
            ->where('is_demo', false)->where('is_auction', false)
            ->when(array_filter((array) ($c['city'] ?? [])), fn ($q, $v) => $q->whereIn(
                \Illuminate\Support\Facades\DB::raw('LOWER(city)'), array_map('mb_strtolower', $v)))
            ->when($c['min'] ?? null, fn ($q, $v) => $q->where('list_price', '>=', (int) $v))
            ->when($c['max'] ?? null, fn ($q, $v) => $q->where('list_price', '<=', (int) $v))
            ->when($c['beds'] ?? null, fn ($q, $v) => $q->where('beds', '>=', (int) $v))
            ->when($c['dwelling'] ?? null, fn ($q, $v) => $q->where('dwelling', $v))
            ->when($c['waterfront'] ?? null, fn ($q) => $q->where('waterfront', true))
            ->when($c['basement'] ?? null, fn ($q) => $q->whereHas('features',
                fn ($f) => $f->where('category', 'basement')->where('value', '!=', 'None')))
            ->when($c['garage'] ?? null, fn ($q, $v) => $q->where('garage_spaces', '>=', (int) $v));
    }
}
