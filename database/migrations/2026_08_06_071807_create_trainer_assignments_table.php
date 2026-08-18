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
        Schema::create('trainer_assignments', function (Blueprint $table) {
            $table->id();
            $table->date('assignment_date');
            $table->date('end_date')->nullable();
            $table->string('goal')->nullable();
            $table->string('reassignment_reason')->nullable();

            $table->foreignId('member_id')->constrained('members', 'user_id')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('trainers', 'user_id')->restrictOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_assignments');
    }
};
