<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMedicineTypeToItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('items', function (Blueprint $table) {
        // We add the column after 'category' and make it nullable 
        // so old items don't break.
        $table->string('medicine_type')->nullable()->after('category');
    });
}

public function down()
{
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn('medicine_type');
    });
}
}
