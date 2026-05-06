<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'medicine_type_id')) {
                $table->foreignId('medicine_type_id')
                    ->nullable()
                    ->after('category')
                    ->constrained('medicine_types')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'medicine_type_id')) {
                $table->dropConstrainedForeignId('medicine_type_id');
            }
        });

        Schema::dropIfExists('medicine_types');
    }
};
