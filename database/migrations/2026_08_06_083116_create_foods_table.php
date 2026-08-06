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
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->decimal('calories_per_serving', 8, 2);
            $table->decimal('protein_g', 8, 2);
            $table->decimal('carbs_g', 8, 2);
            $table->decimal('fat_g', 8, 2);
            $table->decimal('reference_serving_g', 8, 2);
            $table->boolean('is_active')->default(true);

            $table->foreignId('category_id')->constrained('food_categories')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
