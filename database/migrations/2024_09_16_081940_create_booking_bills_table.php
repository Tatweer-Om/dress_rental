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
        Schema::create('booking_bills', function (Blueprint $table) {
            $table->id();
            $table->integer('booking_id'); // Foreign key to bookings table
            $table->string('booking_no'); // Booking number
            $table->decimal('total_price', 10, 3); // Total price, 3 digits after decimal
            $table->decimal('total_discount', 10, 3)->nullable(); // Total discount
            $table->decimal('total_penalty', 10, 3)->nullable(); // Total penalty
            $table->decimal('grand_total', 10, 3); // Grand total after discounts/penalties
            $table->decimal('total_remaining', 10, 3); // Remaining amount
            $table->integer('user_id'); // User ID
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
        Schema::dropIfExists('booking_bills');
    }
};
