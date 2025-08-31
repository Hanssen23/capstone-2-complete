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
        Schema::create('rfid_logs', function (Blueprint $table) {
            $table->id();
            $table->string('card_uid');
            $table->enum('action', ['check_in', 'check_out', 'unknown_card', 'expired_membership', 'payment_due']);
            $table->enum('status', ['success', 'failed', 'warning']);
            $table->text('message');
            $table->json('card_data')->nullable(); // Store any additional card information
            $table->timestamp('timestamp');
            $table->string('device_id')->nullable(); // In case you have multiple RFID readers
            $table->timestamps();
            
            $table->index(['card_uid', 'timestamp']);
            $table->index(['action', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_logs');
    }
};
