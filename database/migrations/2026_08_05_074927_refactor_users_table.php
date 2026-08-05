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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 255)->after('id');
            $table->string('last_name', 255)->after('first_name');
            $table->string('phone_number', 20)->nullable()->after('last_name');
            $table->boolean('is_active')->default(true)->after('phone_number');

            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'phone_number', 'is_active', 'role_id']);
            $table->string('name');
        });
    }
};
