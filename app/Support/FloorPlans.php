<?php

namespace App\Support;

/**
 * Builder floor-plan library, keyed by subdivision slug. Content nobody
 * else has: scans from original builder brochures collected over 26 years
 * of selling these communities, digitized one listing at a time. Add a
 * cleaned JPEG under public/images/floorplans/ and register it here — the
 * subdivision page grows a "Floor plans" section, and team listings in
 * that subdivision link to it.
 */
class FloorPlans
{
    private const PLANS = [
        'serendipity-buffalo-grove' => [
            [
                'model' => 'Ashford',
                'style' => 'Attached ranch · 2 bedrooms · 1 bath (optional second bath)',
                'image' => '/images/floorplans/serendipity-ashford.jpg',
                'facts' => [
                    'Master bedroom' => "12'3\" × 15'2\"",
                    'Bedroom 2' => "11'3\" × 11'7\"",
                    'Living room' => "12'0\" × 15'7\" · cathedral ceiling",
                    'Dining room' => "10'6\" × 11'6\"",
                    'Kitchen' => "10'2\" × 13'0\" · pass-thru",
                    'Garage' => "10'2\" × 19'0\" · attached",
                ],
                'notes' => 'Cathedral ceilings in the living and dining rooms, in-unit washer/dryer, optional fireplace, and an optional second bath off the master.',
            ],
        ],
    ];

    /** @return array[] the registered plans for one subdivision slug */
    public static function for(?string $slug): array
    {
        return self::PLANS[$slug] ?? [];
    }
}
