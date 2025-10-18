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
            if (!Schema::hasColumn('payments', 'amount_tendered')) {
                $table->decimal('amount_tendered', 10, 2)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('payments', 'change_amount')) {
                $table->decimal('change_amount', 10, 2)->default(0.00)->after('amount_tendered');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['amount_tendered', 'change_amount']);
        });
    }
};

