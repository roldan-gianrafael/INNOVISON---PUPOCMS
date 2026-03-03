<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('consultations', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Pangalan ng pasyente o pamagat ng consultation
        $table->date('consultation_date');
        $table->string('user_role'); // e.g., Student, Faculty, Staff
        
        // Relationship
        $table->foreignId('medical_condition_id')->constrained()->onDelete('cascade');
        
        // Medical Data
        $table->decimal('temperature', 4, 2); // Halimbawa: 37.50
        $table->string('medicine')->nullable();
        $table->integer('medicine_quantity')->default(0);
        $table->text('comments')->nullable();
        
        $table->timestamps();   // created_at at updated_at
        $table->softDeletes();  // deleted_at
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consultations');
    }
}
