<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAdminHubMembershipToAdminsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'admin_hub_enabled')) {
                $table->boolean('admin_hub_enabled')->default(false)->after('access_level');
            }

            if (!Schema::hasColumn('admins', 'admin_hub_role')) {
                $table->string('admin_hub_role', 50)->nullable()->after('admin_hub_enabled');
            }
        });

        // Preserve only records that clearly came from an explicit external
        // directory lookup. Local access_level values may have been generated
        // automatically and must not imply Admin Hub membership.
        if (Schema::hasColumn('admins', 'external_identifier')) {
            DB::table('admins')
                ->where('access_level', 'designee')
                ->whereNotNull('external_identifier')
                ->where('external_identifier', '<>', '')
                ->update([
                    'admin_hub_enabled' => true,
                    'admin_hub_role' => 'admin_designee',
                ]);
        }
    }

    public function down()
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('admins', 'admin_hub_role')) {
                $columns[] = 'admin_hub_role';
            }

            if (Schema::hasColumn('admins', 'admin_hub_enabled')) {
                $columns[] = 'admin_hub_enabled';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
}
