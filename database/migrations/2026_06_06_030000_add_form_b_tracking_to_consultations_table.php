<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (!Schema::hasColumn('consultations', 'item_id')) {
                $table->foreignId('item_id')
                    ->nullable()
                    ->after('medicine')
                    ->constrained('items')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('consultations', 'attending_staff_id')) {
                $table->foreignId('attending_staff_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('consultations', 'attending_staff_name')) {
                $table->string('attending_staff_name')
                    ->nullable()
                    ->after('attending_staff_id');
            }

            if (!Schema::hasColumn('consultations', 'time_in')) {
                $table->time('time_in')->nullable()->after('consultation_date');
            }

            if (!Schema::hasColumn('consultations', 'time_out')) {
                $table->time('time_out')->nullable()->after('time_in');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (Schema::hasColumn('consultations', 'item_id')) {
                $table->dropForeign(['item_id']);
                $table->dropColumn('item_id');
            }

            if (Schema::hasColumn('consultations', 'attending_staff_id')) {
                $table->dropForeign(['attending_staff_id']);
                $table->dropColumn('attending_staff_id');
            }

            foreach (['attending_staff_name', 'time_out', 'time_in'] as $column) {
                if (Schema::hasColumn('consultations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
