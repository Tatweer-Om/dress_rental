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
        Schema::create('booking_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('booking_id')->nullable(); // Foreign key to bookings table
            $table->string('booking_no')->nullable(); // Booking number
            $table->integer('bill_id')->nullable(); // Booking number
            $table->integer('customer_id')->nullable(); // Booking number
            $table->decimal('total_amount', 10, 3)->nullable(); // Total price, 3 digits after decimal
            $table->decimal('paid_amount', 10, 3)->nullable(); // Total discount
            $table->decimal('remaining_amount', 10, 3)->nullable(); // Total penalty
            $table->date('payment_date')->nullable(); // Grand total after discounts/penalties
            $table->integer('payment_method')->nullable(); // Remaining amount
            $table->longText('notes')->nullable(); // Remaining amount
            $table->integer('user_id')->nullable(); // User ID
            $table->string('added_by')->nullable(); // Added by user
            $table->string('updated_by')->nullable(); // Updated by user
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_payments');
    }
};
