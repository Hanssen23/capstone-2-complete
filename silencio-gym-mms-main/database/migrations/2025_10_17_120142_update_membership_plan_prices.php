<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update membership plan prices to match the config file
        DB::table('membership_plans')->where('name', 'Basic')->update(['price' => 900.00]);
        DB::table('membership_plans')->where('name', 'VIP')->update(['price' => 1250.00]);
        DB::table('membership_plans')->where('name', 'Premium')->update(['price' => 950.00]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old prices
        DB::table('membership_plans')->where('name', 'Basic')->update(['price' => 700.00]);
        DB::table('membership_plans')->where('name', 'VIP')->update(['price' => 1900.00]);
        DB::table('membership_plans')->where('name', 'Premium')->update(['price' => 3300.00]);
    }
};
