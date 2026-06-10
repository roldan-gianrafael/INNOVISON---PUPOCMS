<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('first_name');
            }
        });

        DB::table('users')
            ->select(['id', 'name', 'first_name', 'last_name'])
            ->whereNull('middle_name')
            ->orderBy('id')
            ->chunkById(200, function ($users) {
                foreach ($users as $user) {
                    $fullName = trim((string) $user->name);
                    $firstName = trim((string) $user->first_name);
                    $lastName = trim((string) $user->last_name);

                    if ($fullName === '' || $firstName === '' || $lastName === '') {
                        continue;
                    }

                    $pattern = '/^' . preg_quote($firstName, '/')
                        . '\s+(.+?)\s+' . preg_quote($lastName, '/') . '$/i';

                    if (preg_match($pattern, $fullName, $matches)) {
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update(['middle_name' => trim($matches[1])]);
                    }
                }
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'middle_name')) {
                $table->dropColumn('middle_name');
            }
        });
    }
};
