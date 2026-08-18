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
        Schema::create('periodic_measurements', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('weight', 5, 2);
            $table->decimal('waist_measurement', 5, 2)->nullable();
            $table->decimal('arm_measurement', 5, 2)->nullable();
            $table->decimal('leg_measurement', 5, 2)->nullable();

            $table->foreignId('trainer_assignment_id')->constrained('trainer_assignments')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periodic_measurements');
    }
};
