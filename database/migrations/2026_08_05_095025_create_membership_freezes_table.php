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
        Schema::create('membership_freezes', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('estimated_reactivation_date')->nullable();
            $table->date('reactivation_date')->nullable();
            $table->string('reason');
            $table->integer('frozen_days')->nullable();

            $table->foreignId('member_membership_id')->constrained('member_memberships')->onDelete('cascade');
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_freezes');
    }
};
