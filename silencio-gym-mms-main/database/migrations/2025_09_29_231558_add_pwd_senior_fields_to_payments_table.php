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
        Schema::table('payments', function (Blueprint $table) {
            // Check if columns don't already exist before adding
            if (!Schema::hasColumn('payments', 'is_pwd')) {
                $table->boolean('is_pwd')->default(false)->after('status');
            }
            if (!Schema::hasColumn('payments', 'is_senior_citizen')) {
                $table->boolean('is_senior_citizen')->default(false)->after('is_pwd');
            }
            if (!Schema::hasColumn('payments', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0.00)->after('is_senior_citizen');
            }
            if (!Schema::hasColumn('payments', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0.00)->after('discount_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['is_pwd', 'is_senior_citizen', 'discount_amount', 'discount_percentage']);
        });
    }
};
