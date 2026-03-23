<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('admins')) {
            return;
        }

        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('admin_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->index();
            $table->date('birthday')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('civil_status')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_person')->nullable();
            $table->string('emergency_contact_no')->nullable();
            $table->string('office')->nullable();
            $table->string('access_level')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
