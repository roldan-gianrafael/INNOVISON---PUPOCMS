<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventory_movements')) {
            return;
        }

        Schema::table('inventory_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_movements', 'movement_date')) {
                $table->date('movement_date')->nullable()->after('type');
            }

            if (!Schema::hasColumn('inventory_movements', 'reason')) {
                $table->string('reason')->nullable()->after('movement_date');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('inventory_movements')) {
            return;
        }

        Schema::table('inventory_movements', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('inventory_movements', 'reason') ? 'reason' : null,
                Schema::hasColumn('inventory_movements', 'movement_date') ? 'movement_date' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
