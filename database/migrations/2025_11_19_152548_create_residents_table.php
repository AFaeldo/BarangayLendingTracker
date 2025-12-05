<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_initial')->nullable();
            $table->enum('gender', ['Male', 'Female']); // Restrict gender values
            $table->date('birth_date')->nullable();      // New birth_date field
            $table->unsignedInteger('age')->nullable();  // Optional, can be calculated
            $table->string('email')->nullable();         // New email field
            $table->string('sitio')->nullable();
            $table->string('contact')->nullable();
            $table->string('status')->default('Active');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
