<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Everything useful from the raw RESO record becomes a typed column, and
     * `raw` goes away entirely — along with the broker-only internals (agent
     * cells, broker notices, owner fields) we'd rather not retain at all.
     * photo_count goes too (media holds the primary entry; galleries re-fetch
     * fresh URLs per listing).
     */
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->unsignedInteger('tax_annual')->nullable()->after('year_built');
            $table->unsignedSmallInteger('tax_year')->nullable()->after('tax_annual');
            $table->unsignedInteger('hoa_fee')->nullable()->after('tax_year');
            $table->string('hoa_fee_freq', 20)->nullable()->after('hoa_fee');
            $table->string('hoa_includes', 255)->nullable()->after('hoa_fee_freq');
            $table->unsignedSmallInteger('parking_total')->nullable()->after('hoa_includes');
            $table->unsignedSmallInteger('garage_spaces')->nullable()->after('parking_total');
            $table->string('heating', 120)->nullable()->after('garage_spaces');
            $table->string('cooling', 120)->nullable()->after('heating');
            $table->string('lot_dimensions', 60)->nullable()->after('cooling');
            $table->string('elementary_district', 10)->nullable()->after('lot_dimensions');
            $table->string('middle_district', 10)->nullable()->after('elementary_district');
            $table->string('high_district', 10)->nullable()->after('middle_district');
            $table->unsignedSmallInteger('rooms_total')->nullable()->after('high_district');
            $table->unsignedSmallInteger('stories')->nullable()->after('rooms_total');
            $table->string('basement', 80)->nullable()->after('stories');
            $table->boolean('new_construction')->default(false)->after('basement');
            $table->date('listing_contract_date')->nullable()->after('new_construction');
            $table->boolean('waterfront')->default(false)->after('listing_contract_date');
            $table->string('ownership', 30)->nullable()->after('waterfront');
        });

        foreach (DB::table('listings')->whereNotNull('raw')->select('id', 'raw')->cursor() as $row) {
            $r = json_decode($row->raw, true) ?: [];
            DB::table('listings')->where('id', $row->id)->update(self::details($r));
        }

        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['raw', 'photo_count']);
        });
        DB::statement('OPTIMIZE TABLE listings');
    }

    public function down(): void
    {
        // raw is re-created by the next mls:sync --full under the old code.
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['tax_annual', 'tax_year', 'hoa_fee', 'hoa_fee_freq', 'hoa_includes',
                'parking_total', 'garage_spaces', 'heating', 'cooling', 'lot_dimensions',
                'elementary_district', 'middle_district', 'high_district', 'rooms_total', 'stories',
                'basement', 'new_construction', 'listing_contract_date', 'waterfront', 'ownership']);
        });
    }

    /** Mirrors MlsSync::extractDetails at the time of this migration. */
    private static function details(array $r): array
    {
        $join = fn ($v, int $max) => $v === null ? null
            : mb_substr(is_array($v) ? implode(', ', $v) : (string) $v, 0, $max);
        $int = fn ($v) => is_numeric($v) && $v >= 0 ? (int) $v : null;

        return [
            'tax_annual' => $int($r['TaxAnnualAmount'] ?? null),
            'tax_year' => $int($r['TaxYear'] ?? null),
            'hoa_fee' => $int($r['AssociationFee'] ?? null),
            'hoa_fee_freq' => $join($r['AssociationFeeFrequency'] ?? null, 20),
            'hoa_includes' => $join($r['AssociationFeeIncludes'] ?? null, 255),
            'parking_total' => $int($r['ParkingTotal'] ?? null),
            'garage_spaces' => $int($r['GarageSpaces'] ?? null),
            'heating' => $join($r['Heating'] ?? null, 120),
            'cooling' => $join($r['Cooling'] ?? null, 120),
            'lot_dimensions' => $join($r['LotSizeDimensions'] ?? null, 60),
            'elementary_district' => $join($r['ElementarySchoolDistrict'] ?? null, 10),
            'middle_district' => $join($r['MiddleOrJuniorSchoolDistrict'] ?? null, 10),
            'high_district' => $join($r['HighSchoolDistrict'] ?? null, 10),
            'rooms_total' => $int($r['RoomsTotal'] ?? null),
            'stories' => $int($r['StoriesTotal'] ?? null),
            'basement' => $join($r['Basement'] ?? null, 80),
            'new_construction' => (bool) ($r['NewConstructionYN'] ?? false),
            'listing_contract_date' => $r['ListingContractDate'] ?? null,
            'waterfront' => (bool) ($r['WaterfrontYN'] ?? false),
            'ownership' => $join($r['Ownership'] ?? null, 30),
        ];
    }
};
