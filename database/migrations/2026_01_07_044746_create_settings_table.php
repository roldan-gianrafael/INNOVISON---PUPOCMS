<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->string('clinic_name')->default('PUP Taguig Clinic');
        $table->string('clinic_location')->default('Santos Ave, Lower Bicutan, Taguig');
        $table->time('open_time')->default('08:00');
        $table->time('close_time')->default('17:00');
        $table->boolean('email_notifications')->default(true);
        $table->boolean('auto_approve')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
