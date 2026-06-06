<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('health_profiles', 'has_illness')) {
                $table->string('has_illness', 10)->nullable();
            }
            if (!Schema::hasColumn('health_profiles', 'medical_history')) {
                $table->json('medical_history')->nullable();
            }
            if (!Schema::hasColumn('health_profiles', 'other_illness')) {
                $table->text('other_illness')->nullable();
            }
            if (!Schema::hasColumn('health_profiles', 'food_allergies')) {
                $table->string('food_allergies')->nullable();
            }
            if (!Schema::hasColumn('health_profiles', 'no_allergies')) {
                $table->boolean('no_allergies')->default(false);
            }
            if (!Schema::hasColumn('health_profiles', 'medicine_allergies')) {
                $table->json('medicine_allergies')->nullable();
            }
            if (!Schema::hasColumn('health_profiles', 'other_med_allergies')) {
                $table->string('other_med_allergies')->nullable();
            }
            if (!Schema::hasColumn('health_profiles', 'is_smoker')) {
                $table->string('is_smoker', 10)->nullable();
            }
            if (!Schema::hasColumn('health_profiles', 'is_drinker')) {
                $table->string('is_drinker', 10)->nullable();
            }
            if (!Schema::hasColumn('health_profiles', 'covid_vaccinated')) {
                $table->string('covid_vaccinated', 10)->nullable();
            }
            if (!Schema::hasColumn('health_profiles', 'vaccine_history')) {
                $table->json('vaccine_history')->nullable();
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'has_illness',
            'medical_history',
            'other_illness',
            'food_allergies',
            'no_allergies',
            'medicine_allergies',
            'other_med_allergies',
            'is_smoker',
            'is_drinker',
            'covid_vaccinated',
            'vaccine_history',
        ];

        $existingColumns = array_values(array_filter(
            $columns,
            static fn (string $column): bool => Schema::hasColumn('health_profiles', $column)
        ));

        if ($existingColumns !== []) {
            Schema::table('health_profiles', function (Blueprint $table) use ($existingColumns) {
                $table->dropColumn($existingColumns);
            });
        }
    }
};
