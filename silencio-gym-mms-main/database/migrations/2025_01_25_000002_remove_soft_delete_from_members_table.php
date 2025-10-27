<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove soft delete columns and implement hard delete for members.
     */
    public function up(): void
    {
        // Simply skip this migration as the columns don't exist
        // This migration was meant to remove soft delete columns that were never added
    }

    /**
     * Reverse the migrations.
     * Re-add soft delete columns if needed.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Re-add soft delete support
            $table->softDeletes()->after('updated_at');
            
            // Re-add deletion tracking fields
            $table->string('deletion_reason')->nullable()->after('deleted_at');
            $table->unsignedBigInteger('deleted_by_admin_id')->nullable()->after('deletion_reason');
            $table->timestamp('deletion_warning_sent_at')->nullable()->after('deleted_by_admin_id');
            $table->timestamp('final_warning_sent_at')->nullable()->after('deletion_warning_sent_at');
        });
    }
};
