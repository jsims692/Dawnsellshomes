<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dwelling classification from MRED's MRD_TYP: detached / attached / multi
     * (2-4 units). City pages default to detached with an attached toggle, and
     * leases, land, and commercial types leave the table entirely — this is an
     * IDX site for homes (objective property-type criterion, Rule 9).
     */
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->string('dwelling', 10)->nullable()->after('property_subtype');
            $table->index(['city', 'dwelling', 'status']);
        });

        // Backfill from the stored raw record, then drop non-dwelling rows.
        DB::statement(<<<'SQL'
            UPDATE listings SET dwelling = CASE JSON_UNQUOTE(JSON_EXTRACT(raw, '$.MRD_TYP'))
                WHEN 'Detached Single' THEN 'detached'
                WHEN 'Attached Single' THEN 'attached'
                WHEN 'Two to Four Units' THEN 'multi'
                ELSE NULL END
            WHERE raw IS NOT NULL
        SQL);
        DB::table('listings')->where('is_demo', false)->whereNull('dwelling')->delete();
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex(['city', 'dwelling', 'status']);
            $table->dropColumn('dwelling');
        });
    }
};
