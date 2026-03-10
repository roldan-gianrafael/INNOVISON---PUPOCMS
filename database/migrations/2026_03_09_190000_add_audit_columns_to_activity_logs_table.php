<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('activity_logs', 'user_role')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('user_role', 50)->nullable()->after('user_name')->index();
            });
        }

        if (!Schema::hasColumn('activity_logs', 'module')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('module', 120)->nullable()->after('action')->index();
            });
        }

        if (!Schema::hasColumn('activity_logs', 'event_type')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('event_type', 50)->nullable()->after('module')->index();
            });
        }

        if (!Schema::hasColumn('activity_logs', 'route_name')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('route_name', 150)->nullable()->after('description')->index();
            });
        }

        if (!Schema::hasColumn('activity_logs', 'http_method')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('http_method', 10)->nullable()->after('route_name')->index();
            });
        }

        if (!Schema::hasColumn('activity_logs', 'request_path')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('request_path', 255)->nullable()->after('http_method')->index();
            });
        }

        if (!Schema::hasColumn('activity_logs', 'status_code')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->unsignedSmallInteger('status_code')->nullable()->after('request_path')->index();
            });
        }

        if (!Schema::hasColumn('activity_logs', 'subject_type')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('subject_type', 120)->nullable()->after('status_code')->index();
            });
        }

        if (!Schema::hasColumn('activity_logs', 'subject_id')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->string('subject_id', 120)->nullable()->after('subject_type')->index();
            });
        }

        if (!Schema::hasColumn('activity_logs', 'metadata')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->json('metadata')->nullable()->after('subject_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('activity_logs', 'metadata')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('metadata');
            });
        }

        if (Schema::hasColumn('activity_logs', 'subject_id')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('subject_id');
            });
        }

        if (Schema::hasColumn('activity_logs', 'subject_type')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('subject_type');
            });
        }

        if (Schema::hasColumn('activity_logs', 'status_code')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('status_code');
            });
        }

        if (Schema::hasColumn('activity_logs', 'request_path')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('request_path');
            });
        }

        if (Schema::hasColumn('activity_logs', 'http_method')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('http_method');
            });
        }

        if (Schema::hasColumn('activity_logs', 'route_name')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('route_name');
            });
        }

        if (Schema::hasColumn('activity_logs', 'event_type')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('event_type');
            });
        }

        if (Schema::hasColumn('activity_logs', 'module')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('module');
            });
        }

        if (Schema::hasColumn('activity_logs', 'user_role')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropColumn('user_role');
            });
        }
    }
};
