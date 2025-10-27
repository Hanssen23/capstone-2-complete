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
        Schema::create('member_deletion_logs', function (Blueprint $table) {
            $table->id();
            
            // Member information (stored for audit trail)
            $table->unsignedBigInteger('member_id');
            $table->string('member_number');
            $table->string('member_name');
            $table->string('member_email');
            $table->string('member_status');
            
            // Deletion details
            $table->enum('deletion_type', ['auto', 'manual', 'admin']);
            $table->string('deletion_reason');
            $table->text('deletion_criteria')->nullable(); // JSON of criteria that triggered deletion
            
            // Admin information (if manually deleted)
            $table->unsignedBigInteger('deleted_by_admin_id')->nullable();
            $table->string('deleted_by_admin_name')->nullable();
            
            // Timing information
            $table->timestamp('member_last_login_at')->nullable();
            $table->timestamp('member_last_activity_at')->nullable();
            $table->timestamp('membership_expired_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            
            // Warning information
            $table->timestamp('first_warning_sent_at')->nullable();
            $table->timestamp('final_warning_sent_at')->nullable();
            $table->boolean('member_reactivated_before_deletion')->default(false);
            
            // Restoration information
            $table->boolean('is_restored')->default(false);
            $table->timestamp('restored_at')->nullable();
            $table->unsignedBigInteger('restored_by_admin_id')->nullable();
            $table->text('restoration_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance and reporting
            $table->index(['member_id']);
            $table->index(['deletion_type']);
            $table->index(['deleted_by_admin_id']);
            $table->index(['created_at']);
            $table->index(['is_restored']);
            $table->index(['member_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_deletion_logs');
    }
};
