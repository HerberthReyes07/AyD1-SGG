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
        Schema::create('membership_status_histories', function (Blueprint $table) {
            $table->id();
            $table->enum('previous_status', ['active', 'frozen', 'expired', 'cancelled'])->nullable();
            $table->enum('new_status', ['active', 'frozen', 'expired', 'cancelled']);
            $table->date('change_date');
            $table->string('reason')->nullable();

            $table->foreignId('changed_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('member_membership_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_status_histories');
    }
};
