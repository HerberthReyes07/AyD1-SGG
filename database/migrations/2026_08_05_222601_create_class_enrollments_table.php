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
        Schema::create('class_enrollments', function (Blueprint $table) {
            $table->id();
            $table->date('enrollment_date');
            $table->enum('status', ['enrolled', 'cancelled', 'attended', 'no_show'])->default('enrolled');

            $table->foreignId('member_id')->constrained('members', 'user_id')->onDelete('cascade');
            $table->foreignId('class_session_id')->constrained('class_sessions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
    }
};
