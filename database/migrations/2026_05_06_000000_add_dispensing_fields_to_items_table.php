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
            if (!Schema::hasColumn('items', 'dispensing_unit')) {
                $table->string('dispensing_unit', 50)->nullable()->after('unit');
            }

            if (!Schema::hasColumn('items', 'units_per_stock_unit')) {
                $table->unsignedInteger('units_per_stock_unit')->nullable()->after('dispensing_unit');
            }
        });

        DB::statement('ALTER TABLE items MODIFY quantity DECIMAL(10,2) NOT NULL DEFAULT 0');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE items MODIFY quantity INT NOT NULL DEFAULT 0');

        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'units_per_stock_unit')) {
                $table->dropColumn('units_per_stock_unit');
            }

            if (Schema::hasColumn('items', 'dispensing_unit')) {
                $table->dropColumn('dispensing_unit');
            }
        });
    }
};
