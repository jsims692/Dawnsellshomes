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
        $parts = [$type.(($c['city'] ?? null) ? ' in '.$c['city'] : ' anywhere we serve')];
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
            ->when($c['city'] ?? null, fn ($q, $v) => $q->where('city', $v))
            ->when($c['min'] ?? null, fn ($q, $v) => $q->where('list_price', '>=', (int) $v))
            ->when($c['max'] ?? null, fn ($q, $v) => $q->where('list_price', '<=', (int) $v))
            ->when($c['beds'] ?? null, fn ($q, $v) => $q->where('beds', '>=', (int) $v))
            ->when($c['dwelling'] ?? null, fn ($q, $v) => $q->where('dwelling', $v));
    }
}
