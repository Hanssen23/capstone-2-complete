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
        // Add indexes to membership_periods table for faster queries
        Schema::table('membership_periods', function (Blueprint $table) {
            // Check if indexes don't already exist before adding
            if (!$this->indexExists('membership_periods', 'idx_membership_periods_member_status')) {
                $table->index(['member_id', 'status'], 'idx_membership_periods_member_status');
            }
            if (!$this->indexExists('membership_periods', 'idx_membership_periods_dates')) {
                $table->index(['start_date', 'expiration_date'], 'idx_membership_periods_dates');
            }
            if (!$this->indexExists('membership_periods', 'idx_membership_periods_status')) {
                $table->index('status', 'idx_membership_periods_status');
            }
        });

        // Add indexes to payments table for faster queries
        Schema::table('payments', function (Blueprint $table) {
            if (!$this->indexExists('payments', 'idx_payments_member_date')) {
                $table->index(['member_id', 'payment_date'], 'idx_payments_member_date');
            }
            if (!$this->indexExists('payments', 'idx_payments_status')) {
                $table->index('status', 'idx_payments_status');
            }
            if (!$this->indexExists('payments', 'idx_payments_plan_type')) {
                $table->index('plan_type', 'idx_payments_plan_type');
            }
        });

        // Add indexes to members table for faster searches
        Schema::table('members', function (Blueprint $table) {
            if (!$this->indexExists('members', 'idx_members_status')) {
                $table->index('status', 'idx_members_status');
            }
            if (!$this->indexExists('members', 'idx_members_subscription_status')) {
                $table->index('subscription_status', 'idx_members_subscription_status');
            }
            if (!$this->indexExists('members', 'idx_members_membership_expires')) {
                $table->index('membership_expires_at', 'idx_members_membership_expires');
            }
        });

        // Add indexes to rfid_logs table for faster queries
        Schema::table('rfid_logs', function (Blueprint $table) {
            if (!$this->indexExists('rfid_logs', 'idx_rfid_logs_member_date')) {
                $table->index(['member_id', 'created_at'], 'idx_rfid_logs_member_date');
            }
            if (!$this->indexExists('rfid_logs', 'idx_rfid_logs_created_at')) {
                $table->index('created_at', 'idx_rfid_logs_created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes from membership_periods table
        Schema::table('membership_periods', function (Blueprint $table) {
            if ($this->indexExists('membership_periods', 'idx_membership_periods_member_status')) {
                $table->dropIndex('idx_membership_periods_member_status');
            }
            if ($this->indexExists('membership_periods', 'idx_membership_periods_dates')) {
                $table->dropIndex('idx_membership_periods_dates');
            }
            if ($this->indexExists('membership_periods', 'idx_membership_periods_status')) {
                $table->dropIndex('idx_membership_periods_status');
            }
        });

        // Drop indexes from payments table
        Schema::table('payments', function (Blueprint $table) {
            if ($this->indexExists('payments', 'idx_payments_member_date')) {
                $table->dropIndex('idx_payments_member_date');
            }
            if ($this->indexExists('payments', 'idx_payments_status')) {
                $table->dropIndex('idx_payments_status');
            }
            if ($this->indexExists('payments', 'idx_payments_plan_type')) {
                $table->dropIndex('idx_payments_plan_type');
            }
        });

        // Drop indexes from members table
        Schema::table('members', function (Blueprint $table) {
            if ($this->indexExists('members', 'idx_members_status')) {
                $table->dropIndex('idx_members_status');
            }
            if ($this->indexExists('members', 'idx_members_subscription_status')) {
                $table->dropIndex('idx_members_subscription_status');
            }
            if ($this->indexExists('members', 'idx_members_membership_expires')) {
                $table->dropIndex('idx_members_membership_expires');
            }
        });

        // Drop indexes from rfid_logs table
        Schema::table('rfid_logs', function (Blueprint $table) {
            if ($this->indexExists('rfid_logs', 'idx_rfid_logs_member_date')) {
                $table->dropIndex('idx_rfid_logs_member_date');
            }
            if ($this->indexExists('rfid_logs', 'idx_rfid_logs_created_at')) {
                $table->dropIndex('idx_rfid_logs_created_at');
            }
        });
    }

    /**
     * Check if an index exists on a table (SQLite compatible)
     */
    private function indexExists(string $table, string $index): bool
    {
        try {
            $connection = Schema::getConnection();
            $indexes = $connection->select("PRAGMA index_list($table)");

            foreach ($indexes as $idx) {
                if ($idx->name === $index) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            // If we can't check, assume it doesn't exist
            return false;
        }
    }
};

