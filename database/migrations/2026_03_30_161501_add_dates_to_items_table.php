<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Add your new columns here
            $table->date('date_added')->nullable()->after('quantity');
            $table->date('expiration_date')->nullable()->after('date_added');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Important: Allow the migration to be reversed
            $table->dropColumn(['date_added', 'expiration_date']);
        });
    }
};