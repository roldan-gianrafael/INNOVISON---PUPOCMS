<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('health_profiles', 'health_form_upload')) {
            return;
        }

        Schema::table('health_profiles', function (Blueprint $table) {
            $table->string('health_form_upload')->nullable()->after('medical_certificate');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('health_profiles', 'health_form_upload')) {
            return;
        }

        Schema::table('health_profiles', function (Blueprint $table) {
            $table->dropColumn('health_form_upload');
        });
    }
};
