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
        Schema::create('booking_extend_histories', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('booking_no'); // Booking number
            $table->Integer('booking_id'); // Foreign key to the bookings table
            $table->Integer('customer_id'); // Foreign key to the customer
            $table->Integer('dress_id'); // Foreign key to the dress
            $table->date('rent_date'); // Rent date (previous return date)
            $table->date('return_date'); // New return date (extended)
            $table->integer('duration'); // Duration (in days)
            $table->decimal('price', 50, 3); // Price for the extended period
            $table->decimal('discount', 50, 3)->nullable(); // Discount applied
            $table->decimal('total_price', 50, 3); // Total price after discount
            $table->tinyInteger('type')->nullable()->comment('1 for before extend, 2 for after extend ');
            $table->text('notes')->nullable(); // Additional notes
            $table->string('added_by'); // Name or role of the person adding the record
            $table->Integer('user_id'); // Foreign key to the user who added it
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_extend_histories');
    }
};
