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
        Schema::create('dress_availabilities', function (Blueprint $table) {
            $table->id();
            $table->string('contact')->nullable(); // Foreign key to bookings table
            $table->integer('dress_id')->nullable(); // Booking number
            $table->tinyInteger('status')->default(1)->comment('1 for new, 2 for sent'); // Status: 1 for pending, 2 for clear
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
        Schema::dropIfExists('dress_availabilities');
    }
};
