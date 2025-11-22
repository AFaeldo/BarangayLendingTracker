<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->string('middle_name')->nullable()->after('first_name');
            $table->string('alias')->nullable()->after('middle_name');

            $table->date('birthdate')->nullable()->after('alias');
            $table->string('place_of_birth')->nullable()->after('birthdate');

            $table->unsignedInteger('age')->nullable()->change(); // kung na-define na dati
            $table->unsignedInteger('age_month')->nullable()->after('age');

            $table->unsignedInteger('height_cm')->nullable()->after('age_month');
            $table->unsignedInteger('weight_kg')->nullable()->after('height_cm');

            $table->string('marital_status')->nullable()->after('gender');
            $table->string('spouse_name')->nullable()->after('marital_status');

            $table->string('purok')->nullable()->after('sitio'); // or gamitin ito imbes na 'sitio'

            $table->string('employment_status')->nullable()->after('purok');
            $table->string('religion')->nullable()->after('employment_status');
            $table->string('voter_status')->nullable()->after('religion');

            $table->boolean('is_pwd')->default(false)->after('voter_status');
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropColumn([
                'middle_name',
                'alias',
                'birthdate',
                'place_of_birth',
                'age_month',
                'height_cm',
                'weight_kg',
                'marital_status',
                'spouse_name',
                'purok',
                'employment_status',
                'religion',
                'voter_status',
                'is_pwd',
            ]);
        });
    }
};
