<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'consumed')) {
                $table->decimal('consumed', 10, 2)->default(0)->after('starting_stock');
            }
        });

        if (Schema::hasColumn('items', 'consumed')) {
            DB::table('items')->update([
                'consumed' => DB::raw('GREATEST(COALESCE(starting_stock, 0) - COALESCE(quantity, 0), 0)'),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'consumed')) {
                $table->dropColumn('consumed');
            }
        });
    }
};
