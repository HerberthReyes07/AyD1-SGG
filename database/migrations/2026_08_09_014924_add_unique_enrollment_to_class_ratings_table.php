<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_ratings', function (Blueprint $table) {
            $table->unique(
                'class_enrollment_id',
                'class_ratings_enrollment_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('class_ratings', function (Blueprint $table) {
            $table->dropUnique(
                'class_ratings_enrollment_unique'
            );
        });
    }
};