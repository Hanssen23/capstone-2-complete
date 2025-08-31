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
        Schema::table('members', function (Blueprint $table) {
            // Add indexes for better query performance
            // $table->index('status'); // Commented out - column doesn't exist yet
            $table->index('membership');
            // $table->index('membership_expires_at'); // Commented out - column doesn't exist yet
            // $table->index(['status', 'membership_expires_at']); // Commented out - columns don't exist yet
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // $table->dropIndex(['status']); // Commented out - column doesn't exist yet
            $table->dropIndex(['membership']);
            // $table->dropIndex(['membership_expires_at']); // Commented out - column doesn't exist yet
            // $table->dropIndex(['status', 'membership_expires_at']); // Commented out - columns don't exist yet
            $table->dropIndex(['created_at']);
        });
    }
};
