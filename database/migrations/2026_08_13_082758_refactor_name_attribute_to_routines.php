<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            $table->dropUnique('routines_name_unique');

            $table->unique(
                ['trainer_assignment_id', 'name'],
                'routines_trainer_assignment_id_name_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            $table->dropUnique(
                'routines_trainer_assignment_id_name_unique'
            );

            $table->unique(
                'name',
                'routines_name_unique'
            );
        });
    }
};