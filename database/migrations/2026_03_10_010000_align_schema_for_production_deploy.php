<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'gender')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('gender')->nullable()->after('DOB');
            });
        }

        if (!Schema::hasColumn('users', 'height')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('height', 5, 2)->nullable()->after('gender');
            });
        }

        if (!Schema::hasColumn('users', 'weight')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('weight', 5, 2)->nullable()->after('height');
            });
        }

        if (!Schema::hasColumn('users', 'contact_no')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('contact_no', 20)->nullable()->after('section');
            });
        }

        if (!Schema::hasColumn('appointments', 'type')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->string('type', 30)->nullable()->after('status');
            });
        }

        if (!Schema::hasColumn('appointments', 'user_type')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->string('user_type', 50)->nullable()->after('type');
            });
        }

        if (!Schema::hasColumn('items', 'description')) {
            Schema::table('items', function (Blueprint $table) {
                $table->text('description')->nullable()->after('quantity');
            });
        }

        if (!Schema::hasColumn('consultations', 'service')) {
            Schema::table('consultations', function (Blueprint $table) {
                $table->string('service')->nullable()->after('user_role');
            });
        }

        if (!Schema::hasColumn('consultations', 'user_type')) {
            Schema::table('consultations', function (Blueprint $table) {
                $table->string('user_type', 50)->nullable()->after('service');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('consultations', 'user_type')) {
            Schema::table('consultations', function (Blueprint $table) {
                $table->dropColumn('user_type');
            });
        }

        if (Schema::hasColumn('consultations', 'service')) {
            Schema::table('consultations', function (Blueprint $table) {
                $table->dropColumn('service');
            });
        }

        if (Schema::hasColumn('items', 'description')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('appointments', 'user_type')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn('user_type');
            });
        }

        if (Schema::hasColumn('appointments', 'type')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasColumn('users', 'contact_no')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('contact_no');
            });
        }

        if (Schema::hasColumn('users', 'weight')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('weight');
            });
        }

        if (Schema::hasColumn('users', 'height')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('height');
            });
        }

        if (Schema::hasColumn('users', 'gender')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('gender');
            });
        }
    }
};
