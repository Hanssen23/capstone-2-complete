<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add indexes to members table - check if they don't already exist
        Schema::table('members', function (Blueprint $table) {
            if (!$this->indexExists('members', ['status', 'membership_expires_at'])) {
                $table->index(['status', 'membership_expires_at']);
            }
            if (!$this->indexExists('members', ['uid'])) {
                // UID index already exists from previous migration
            }
            if (!$this->indexExists('members', ['member_number'])) {
                $table->index(['member_number']);
            }
            if (!$this->indexExists('members', ['email'])) {
                $table->index(['email']);
            }
            if (!$this->indexExists('members', ['current_membership_period_id'])) {
                $table->index(['current_membership_period_id']);
            }
        });

        // Add indexes to payments table
        Schema::table('payments', function (Blueprint $table) {
            if (!$this->indexExists('payments', ['member_id', 'status'])) {
                $table->index(['member_id', 'status']);
            }
            if (!$this->indexExists('payments', ['payment_date'])) {
                $table->index(['payment_date']);
            }
            if (!$this->indexExists('payments', ['membership_expiration_date'])) {
                $table->index(['membership_expiration_date']);
            }
        });

        // Add indexes to attendances table
        Schema::table('attendances', function (Blueprint $table) {
            if (!$this->indexExists('attendances', ['member_id', 'check_in_time'])) {
                $table->index(['member_id', 'check_in_time']);
            }
            if (!$this->indexExists('attendances', ['check_in_time'])) {
                $table->index(['check_in_time']);
            }
        });

        // Add indexes to active_sessions table
        Schema::table('active_sessions', function (Blueprint $table) {
            if (!$this->indexExists('active_sessions', ['member_id', 'status'])) {
                $table->index(['member_id', 'status']);
            }
            if (!$this->indexExists('active_sessions', ['status'])) {
                $table->index(['status']);
            }
        });

        // Add indexes to membership_periods table
        Schema::table('membership_periods', function (Blueprint $table) {
            if (!$this->indexExists('membership_periods', ['member_id', 'status', 'expiration_date'])) {
                $table->index(['member_id', 'status', 'expiration_date']);
            }
            if (!$this->indexExists('membership_periods', ['plan_type', 'status'])) {
                $table->index(['plan_type', 'status']);
            }
        });

        // Add indexes to rfid_logs table
        Schema::table('rfid_logs', function (Blueprint $table) {
            if (!$this->indexExists('rfid_logs', ['card_uid', 'timestamp'])) {
                $table->index(['card_uid', 'timestamp']);
            }
            if (!$this->indexExists('rfid_logs', ['timestamp'])) {
                $table->index(['timestamp']);
            }
        });
    }
    
    private function indexExists($table, $columns)
    {
        $indexName = $table . '_' . implode('_', $columns) . '_index';
        return Schema::hasIndex($table, $indexName);
    }

    public function down()
    {
        // Remove indexes from members table
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['status', 'membership_expires_at']);
            $table->dropIndex(['uid']);
            $table->dropIndex(['member_number']);
            $table->dropIndex(['email']);
            $table->dropIndex(['current_membership_period_id']);
        });

        // Remove indexes from payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['member_id', 'status']);
            $table->dropIndex(['payment_date']);
            $table->dropIndex(['membership_expiration_date']);
        });

        // Remove indexes from attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['member_id', 'check_in_time']);
            $table->dropIndex(['check_in_time']);
        });

        // Remove indexes from active_sessions table
        Schema::table('active_sessions', function (Blueprint $table) {
            $table->dropIndex(['member_id', 'status']);
            $table->dropIndex(['status']);
        });

        // Remove indexes from membership_periods table
        Schema::table('membership_periods', function (Blueprint $table) {
            $table->dropIndex(['member_id', 'status', 'expiration_date']);
            $table->dropIndex(['plan_type', 'status']);
        });

        // Remove indexes from rfid_logs table
        Schema::table('rfid_logs', function (Blueprint $table) {
            $table->dropIndex(['card_uid', 'timestamp']);
            $table->dropIndex(['timestamp']);
        });
    }
};
