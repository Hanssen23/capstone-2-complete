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
        // Add new columns
        Schema::table('members', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('membership');
            $table->string('last_name')->nullable()->after('first_name');
        });

        // Split existing full_name into first_name and last_name
        DB::table('members')->get()->each(function ($member) {
            $names = explode(' ', $member->full_name, 2);
            $firstName = $names[0] ?? '';
            $lastName = $names[1] ?? '';

            DB::table('members')
                ->where('id', $member->id)
                ->update([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                ]);
        });

        // Make the columns required after data migration
        Schema::table('members', function (Blueprint $table) {
            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
        });

        // Remove the old column
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Add back the full_name column
            $table->string('full_name')->nullable()->after('membership');
        });

        // Combine first_name and last_name back into full_name
        DB::table('members')->get()->each(function ($member) {
            DB::table('members')
                ->where('id', $member->id)
                ->update([
                    'full_name' => trim($member->first_name . ' ' . $member->last_name),
                ]);
        });

        // Make full_name required and remove the new columns
        Schema::table('members', function (Blueprint $table) {
            $table->string('full_name')->nullable(false)->change();
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};