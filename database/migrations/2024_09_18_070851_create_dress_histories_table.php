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
        Schema::create('dress_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('booking_id')->nullable(); // Foreign key to bookings table
            $table->string('booking_no')->nullable(); // Booking number
            $table->integer('customer_id')->nullable(); // Booking number
            $table->integer('dress_id')->nullable(); // Booking number
            $table->tinyInteger('type')->nullable()->comment('1 for going, 2 for coming'); // Booking status
            $table->string('source')->nullable(); // Booking number
            $table->date('history_date')->nullable(); // Booking number
            $table->longText('notes')->nullable(); // Remaining amount
            $table->integer('user_id')->nullable(); // User ID
            $table->string('added_by')->nullable(); // Added by user
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dress_histories');
    }
};
