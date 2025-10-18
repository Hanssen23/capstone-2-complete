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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->timestamp('check_in_time');
            $table->timestamp('check_out_time')->nullable();
            $table->enum('status', ['checked_in', 'checked_out'])->default('checked_in');
            $table->string('session_duration')->nullable(); // Calculated field
            $table->timestamps();
            
            $table->index(['member_id', 'check_in_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
