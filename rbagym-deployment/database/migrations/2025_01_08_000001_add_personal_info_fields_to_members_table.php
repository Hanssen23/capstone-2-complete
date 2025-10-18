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
            // Add middle name field after first_name
            $table->string('middle_name')->nullable()->after('first_name');
            
            // Add age field after last_name - REQUIRED
            $table->integer('age')->after('last_name');
            
            // Add gender field after age - REQUIRED
            $table->enum('gender', ['Male', 'Female', 'Other', 'Prefer not to say'])->after('age');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['middle_name', 'age', 'gender']);
        });
    }
};
