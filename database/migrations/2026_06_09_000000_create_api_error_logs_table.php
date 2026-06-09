<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('system_name')->index();
            $table->string('endpoint')->nullable();
            $table->string('error_code')->nullable();
            $table->text('error_message');
            $table->text('request_payload')->nullable();
            $table->text('response_payload')->nullable();
            $table->string('http_status')->nullable();
            $table->string('error_type')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('created_at');
            $table->index(['system_name', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_error_logs');
    }
};
