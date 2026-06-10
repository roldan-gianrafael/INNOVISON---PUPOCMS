<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'reference_number')) {
            return;
        }

        DB::table('users')
            ->whereNotNull('reference_number')
            ->where(function ($query) {
                $query->whereColumn('reference_number', 'student_id')
                    ->orWhereRaw(
                        "reference_number REGEXP '^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$'"
                    );
            })
            ->update(['reference_number' => null]);
    }

    public function down(): void
    {
        // Invalid UUID values must not be restored as admission references.
    }
};
