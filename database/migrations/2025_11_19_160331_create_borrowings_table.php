<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained()->onDelete('cascade');
            // We cannot add constrained item_id here because items table doesn't exist yet!
            // We will add it in a later migration.
            // $table->foreignId('item_id')->constrained()->onDelete('cascade'); 
            
            $table->integer('quantity')->default(1); 
            $table->date('date_borrowed')->nullable(false);
            $table->date('due_date')->nullable();
            $table->enum('status', ['Borrowed','Returned','Overdue'])->default('Borrowed');
            $table->text('remarks')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};