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
            $table->foreignId('current_membership_period_id')->nullable()->constrained('membership_periods')->onDelete('set null');
            $table->date('membership_starts_at')->nullable();
            $table->string('current_plan_type')->nullable(); // basic, vip, premium
            $table->string('current_duration_type')->nullable(); // monthly, quarterly, biannually, annually
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['current_membership_period_id']);
            $table->dropColumn(['current_membership_period_id', 'membership_starts_at', 'current_plan_type', 'current_duration_type']);
        });
    }
};
