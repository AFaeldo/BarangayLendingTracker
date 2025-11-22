<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // ✅ if item was lost (1) or not (0)
            $table->boolean('is_lost')
                  ->default(false)
                  ->after('status');

            // ✅ condition of item when returned (Good, For Repair, Damaged, etc.)
            $table->string('condition_returned')
                  ->nullable()
                  ->after('is_lost');

            // ✅ who processed the return (optional)
            $table->string('received_by')
                  ->nullable()
                  ->after('condition_returned');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn(['is_lost', 'condition_returned', 'received_by']);
        });
    }
};
