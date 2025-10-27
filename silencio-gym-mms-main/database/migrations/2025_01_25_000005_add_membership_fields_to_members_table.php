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
            $table->foreignId('membership_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('membership_expires_at')->nullable();
            $table->enum('status', ['active', 'inactive', 'expired', 'suspended'])->default('active');
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['membership_plan_id']);
            $table->dropColumn(['membership_plan_id', 'membership_expires_at', 'status', 'notes']);
        });
    }
};
