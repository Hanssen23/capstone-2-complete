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
            // Add password column if it doesn't exist
            if (!Schema::hasColumn('members', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            
            // Add remember_token column if it doesn't exist
            if (!Schema::hasColumn('members', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
            
            // Add role column if it doesn't exist
            if (!Schema::hasColumn('members', 'role')) {
                $table->string('role')->default('member')->after('remember_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('members', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
            if (Schema::hasColumn('members', 'password')) {
                $table->dropColumn('password');
            }
        });
    }
};
