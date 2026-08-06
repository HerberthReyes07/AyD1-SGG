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
        Schema::create('calorie_goals', function (Blueprint $table) {
            $table->id();
            $table->decimal('daily_calories', 8, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->foreignId('member_id')->constrained('members', 'user_id')->cascadeOnDelete();
            $table->foreignId('defined_by')->nullable()->constrained('users', 'id')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calorie_goals');
    }
};
