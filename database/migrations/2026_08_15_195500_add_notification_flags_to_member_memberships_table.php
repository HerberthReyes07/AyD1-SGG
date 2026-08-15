<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add two boolean flags to member_memberships to track which
     * expiration-related notification emails have already been sent.
     *
     * expiration_warning_sent  – true once the 5-day warning email is sent.
     * expiration_notified      – true once the expiration-day email is sent.
     *
     * Storing the flags here (instead of a generic notifications table) is the
     * simplest approach and keeps deduplication logic
     * co-located with the membership data.
     */
    public function up(): void
    {
        Schema::table('member_memberships', function (Blueprint $table) {
            $table->boolean('expiration_warning_sent')
                ->default(false)
                ->after('cancellation_date');

            $table->boolean('expiration_notified')
                ->default(false)
                ->after('expiration_warning_sent');
        });
    }

    public function down(): void
    {
        Schema::table('member_memberships', function (Blueprint $table) {
            $table->dropColumn(['expiration_warning_sent', 'expiration_notified']);
        });
    }
};
