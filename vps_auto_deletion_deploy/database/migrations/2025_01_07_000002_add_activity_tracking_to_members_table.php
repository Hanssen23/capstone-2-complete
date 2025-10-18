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
            // Add activity tracking fields
            $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            $table->timestamp('last_activity_at')->nullable()->after('last_login_at');
            
            // Add soft delete support
            $table->softDeletes()->after('updated_at');
            
            // Add deletion tracking fields
            $table->string('deletion_reason')->nullable()->after('deleted_at');
            $table->unsignedBigInteger('deleted_by_admin_id')->nullable()->after('deletion_reason');
            $table->timestamp('deletion_warning_sent_at')->nullable()->after('deleted_by_admin_id');
            $table->timestamp('final_warning_sent_at')->nullable()->after('deletion_warning_sent_at');
            
            // Add auto-deletion configuration fields
            $table->boolean('exclude_from_auto_deletion')->default(false)->after('final_warning_sent_at');
            $table->text('exclusion_reason')->nullable()->after('exclude_from_auto_deletion');
            
            // Add indexes for performance
            $table->index(['last_login_at']);
            $table->index(['last_activity_at']);
            $table->index(['deleted_at']);
            $table->index(['exclude_from_auto_deletion']);
            $table->index(['status', 'last_login_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['last_login_at']);
            $table->dropIndex(['last_activity_at']);
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['exclude_from_auto_deletion']);
            $table->dropIndex(['status', 'last_login_at']);
            
            $table->dropColumn([
                'last_login_at',
                'last_activity_at',
                'deleted_at',
                'deletion_reason',
                'deleted_by_admin_id',
                'deletion_warning_sent_at',
                'final_warning_sent_at',
                'exclude_from_auto_deletion',
                'exclusion_reason'
            ]);
        });
    }
};
