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
            $table->dropColumn('name');
            $table->string('name')->after('trainer_assignment_id');
            $table->unique(['trainer_assignment_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routines', function (Blueprint $table) {
            $table->string('name')->after('trainer_assignment_id');
            $table->dropUnique(['trainer_assignment_id', 'name']);
        });
    }
};
