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
        Schema::create('auto_deletion_settings', function (Blueprint $table) {
            $table->id();
            
            // Feature control
            $table->boolean('is_enabled')->default(false);
            $table->boolean('dry_run_mode')->default(true); // Test mode - log but don't delete
            
            // Inactivity thresholds (in days)
            $table->integer('no_login_threshold_days')->default(365); // 1 year
            $table->integer('expired_membership_grace_days')->default(90); // 3 months
            $table->integer('unverified_email_threshold_days')->default(30); // 1 month
            $table->integer('inactive_status_threshold_days')->default(180); // 6 months
            
            // Warning schedule (days before deletion)
            $table->integer('first_warning_days')->default(30); // 30 days before
            $table->integer('final_warning_days')->default(7); // 7 days before
            
            // Exclusion rules
            $table->boolean('exclude_vip_members')->default(true);
            $table->boolean('exclude_members_with_payments')->default(true);
            $table->boolean('exclude_recent_activity')->default(true); // RFID activity
            $table->integer('recent_activity_threshold_days')->default(30);
            
            // Email settings
            $table->boolean('send_warning_emails')->default(true);
            $table->string('warning_email_from')->default('noreply@silencio-gym.com');
            $table->string('warning_email_from_name')->default('Silencio Gym');
            
            // Scheduling
            $table->string('schedule_frequency')->default('daily'); // daily, weekly
            $table->string('schedule_time')->default('02:00'); // 2 AM
            
            // Audit
            $table->unsignedBigInteger('last_updated_by_admin_id')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->integer('last_run_processed_count')->default(0);
            $table->integer('last_run_deleted_count')->default(0);
            $table->integer('last_run_warned_count')->default(0);
            
            $table->timestamps();
        });
        
        // Insert default settings
        DB::table('auto_deletion_settings')->insert([
            'is_enabled' => false,
            'dry_run_mode' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_deletion_settings');
    }
};
