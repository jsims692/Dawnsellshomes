<?php

namespace App\Console\Commands;

use App\Models\Sale;
use Illuminate\Console\Command;

class ImportSales extends Command
{
    protected $signature = 'import:sales
        {source=/home/jsims/dawnsellshomes-old/sold.html : Static sold.html containing the DEALS array}
        {--fresh : Truncate the sales table first}';

    protected $description = 'One-time import of the historical DEALS dataset from the static sold page into the sales table';

    public function handle(): int
    {
        $html = file_get_contents($this->argument('source'));
        if (! preg_match('/const DEALS = (\[.*?\]);/s', $html, $m)) {
            $this->error('DEALS array not found in source.');

            return self::FAILURE;
        }

        $deals = json_decode($m[1], true, 512, JSON_THROW_ON_ERROR);

        if ($this->option('fresh')) {
            Sale::truncate();
        }

        $rows = collect($deals)->map(fn ($d) => [
            'address' => trim($d['addr']),
            'city' => trim($d['city']),
            'state' => 'IL',
            'sold_price' => (int) $d['sold'],
            'sold_year' => (int) $d['year'],
            'property_type' => $d['type'],
            'side' => $d['side'],
            'lat' => $d['lat'] ?? null,
            'lng' => $d['lng'] ?? null,
            'is_public' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($rows->chunk(200) as $chunk) {
            Sale::insert($chunk->all());
        }
        // Bulk insert bypasses model events, so drop the map cache explicitly.
        cache()->forget('sales-map-payload');

        $this->info(sprintf(
            'Imported %d sales: %d listing-side, %d buyer-side, %d cities, $%sM volume, %d–%d.',
            Sale::count(),
            Sale::where('side', 'listing')->count(),
            Sale::where('side', 'buyside')->count(),
            Sale::distinct('city')->count('city'),
            number_format(Sale::sum('sold_price') / 1e6, 1),
            Sale::min('sold_year'),
            Sale::max('sold_year'),
        ));

        return self::SUCCESS;
    }
}
