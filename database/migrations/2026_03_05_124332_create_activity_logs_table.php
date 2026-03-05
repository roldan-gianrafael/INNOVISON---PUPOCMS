<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            // Naka-nullable para kung mabura ang student, nandun pa rin ang log record
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_name'); // Sine-save ang name para madaling basahin sa table
            $table->string('action');    // Halimbawa: 'Profile Update', 'Login', 'Appointment Booked'
            $table->text('description'); // Detalye ng ginawa
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable(); // Browser details
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};