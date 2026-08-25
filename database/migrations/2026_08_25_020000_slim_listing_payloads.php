<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Slim stored listing payloads. `raw` keeps the not-yet-extracted fields a
     * future detail page needs (taxes, HOA, schools, parking…) but drops its
     * two duplications: the Media array (in `media`/local photo cache) and
     * PublicRemarks (in `remarks`). `media` keeps only the primary entry —
     * MLS GRID URLs expire within the hour, so a stored gallery is dead
     * weight; galleries re-fetch fresh URLs per listing when built.
     */
    public function up(): void
    {
        DB::statement("UPDATE listings SET raw = JSON_REMOVE(raw, '$.Media', '$.PublicRemarks') WHERE raw IS NOT NULL");
        DB::statement("UPDATE listings SET media = JSON_ARRAY(JSON_EXTRACT(media, '$[0]')) WHERE JSON_LENGTH(media) > 1");
        DB::statement('OPTIMIZE TABLE listings');
    }

    public function down(): void
    {
        // Dropped duplications are re-created by the next mls:sync --full.
    }
};
