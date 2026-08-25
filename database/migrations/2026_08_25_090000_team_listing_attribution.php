<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Agent-level attribution so the team's own listings (list, co-list or
     *  buyer side) surface dynamically — homepage "recent results" etc.
     *  IDs are used for matching only, never displayed. */
    public function up(): void
    {
        foreach ([
            'list_agent_id' => fn (Blueprint $t) => $t->string('list_agent_id', 20)->nullable()->after('list_office_email'),
            'colist_agent_id' => fn (Blueprint $t) => $t->string('colist_agent_id', 20)->nullable()->after('list_agent_id'),
            'buyer_agent_id' => fn (Blueprint $t) => $t->string('buyer_agent_id', 20)->nullable()->after('colist_agent_id'),
            'is_team' => fn (Blueprint $t) => $t->boolean('is_team')->default(false)->after('is_auction')->index(),
        ] as $column => $add) {
            if (! Schema::hasColumn('listings', $column)) {
                Schema::table('listings', fn (Blueprint $t) => $add($t));
            }
        }
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['list_agent_id', 'colist_agent_id', 'buyer_agent_id', 'is_team']);
        });
    }
};
