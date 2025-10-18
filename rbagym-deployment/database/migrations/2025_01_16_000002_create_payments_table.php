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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->time('payment_time');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('plan_type'); // basic, vip, premium
            $table->string('duration_type'); // monthly, quarterly, biannually, annually
            $table->date('membership_start_date');
            $table->date('membership_expiration_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['member_id', 'payment_date']);
            $table->index(['status', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
