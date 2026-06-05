<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('health_profiles', 'medical_condition_remarks')) {
                $table->text('medical_condition_remarks')->nullable()->after('pending_reason');
            }

            if (!Schema::hasColumn('health_profiles', 'physical_assessment_status')) {
                $table->string('physical_assessment_status')->nullable()->after('medical_condition_remarks');
            }

            if (!Schema::hasColumn('health_profiles', 'documents_valid')) {
                $table->boolean('documents_valid')->default(false)->after('physical_assessment_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('health_profiles', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('health_profiles', 'documents_valid') ? 'documents_valid' : null,
                Schema::hasColumn('health_profiles', 'physical_assessment_status') ? 'physical_assessment_status' : null,
                Schema::hasColumn('health_profiles', 'medical_condition_remarks') ? 'medical_condition_remarks' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
