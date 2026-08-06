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
        Schema::create('meal_foods', function (Blueprint $table) {
            $table->id();
            $table->decimal('quantity_g', 6, 2);

            $table->foreignId('meal_id')->constrained('meals')->cascadeOnDelete();
            $table->foreignId('food_id')->constrained('foods')->restrictOnDelete();
            $table->timestamps();

            $table->unique(['meal_id', 'food_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meal_foods');
    }
};
