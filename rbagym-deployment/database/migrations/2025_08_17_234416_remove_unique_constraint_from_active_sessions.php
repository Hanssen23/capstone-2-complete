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
        // Drop the existing table and recreate it without the unique constraint
        Schema::dropIfExists('active_sessions');
        
        Schema::create('active_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
            $table->timestamp('check_in_time');
            $table->timestamp('check_out_time')->nullable();
            $table->string('status')->default('active'); // active, inactive
            $table->string('session_duration')->nullable(); // calculated duration
            $table->timestamps();

            $table->index(['member_id', 'status']);
            $table->index(['status', 'check_in_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_sessions');
    }
};
