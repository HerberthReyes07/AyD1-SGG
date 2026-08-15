<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('class_enrollment_id')
                ->unique()
                ->constrained('class_enrollments')
                ->cascadeOnDelete();

            $table->dateTime('check_in_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_attendances');
    }
};