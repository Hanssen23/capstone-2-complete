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
        // First, update existing records with NULL values to have default values
        DB::table('members')->whereNull('age')->update(['age' => 18]);
        DB::table('members')->whereNull('gender')->update(['gender' => 'Prefer not to say']);

        Schema::table('members', function (Blueprint $table) {
            // Make age field required (not nullable)
            $table->integer('age')->nullable(false)->change();

            // Make gender field required (not nullable)
            $table->enum('gender', ['Male', 'Female', 'Other', 'Prefer not to say'])->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Revert age field to nullable
            $table->integer('age')->nullable()->change();
            
            // Revert gender field to nullable
            $table->enum('gender', ['Male', 'Female', 'Other', 'Prefer not to say'])->nullable()->change();
        });
    }
};
