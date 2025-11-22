<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('borrowings', function (Blueprint $table) {
        // optional: remove old item_name column kung meron pa
        if (Schema::hasColumn('borrowings', 'item_name')) {
            $table->dropColumn('item_name');
        }

        // ✅ nullable para di mag-error sa existing rows
        $table->foreignId('item_id')
            ->nullable()
            ->after('resident_id')
            ->constrained('items')
            ->nullOnDelete();
    });
}


  public function down(): void
{
    Schema::table('borrowings', function (Blueprint $table) {
        // drop foreign key & column
        if (Schema::hasColumn('borrowings', 'item_id')) {
            $table->dropForeign(['item_id']);
            $table->dropColumn('item_id');
        }

        // optional: ibalik old item_name
        if (!Schema::hasColumn('borrowings', 'item_name')) {
            $table->string('item_name')->nullable();
        }
    });
}

};
