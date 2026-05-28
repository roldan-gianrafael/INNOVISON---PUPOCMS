<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'starting_stock')) {
                $table->decimal('starting_stock', 10, 2)->default(0)->after('quantity');
            }

            if (!Schema::hasColumn('items', 'minimum_stock')) {
                $table->decimal('minimum_stock', 10, 2)->default(10)->after('starting_stock');
            }

            if (!Schema::hasColumn('items', 'batch_number')) {
                $table->string('batch_number', 120)->nullable()->after('units_per_stock_unit');
            }

            if (!Schema::hasColumn('items', 'supplier_source')) {
                $table->string('supplier_source', 255)->nullable()->after('batch_number');
            }
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 40);
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('stock_before', 10, 2)->default(0);
            $table->decimal('stock_after', 10, 2)->default(0);
            $table->string('unit', 50)->nullable();
            $table->string('batch_number', 120)->nullable();
            $table->string('supplier_source', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');

        Schema::table('items', function (Blueprint $table) {
            foreach (['supplier_source', 'batch_number', 'minimum_stock', 'starting_stock'] as $column) {
                if (Schema::hasColumn('items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
