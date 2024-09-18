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
        Schema::create('booking_dress_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('booking_no')->nullable(); // Booking number
            $table->integer('booking_id')->nullable(); // Foreign key to bookings table
            $table->integer('dress_id')->nullable(); // Foreign key to dresses table
            $table->integer('attribute_id')->nullable(); // Attribute ID (reference to the attribute)
            $table->string('attribute_name')->nullable(); // Attribute name
            $table->longText('attribute_notes')->nullable(); // Notes about the attribute (optional)
            $table->decimal('penalty_price', 8, 3)->nullable(); // Penalty price (optional), 3 digits after the decimal
            $table->tinyInteger('status')->default(1)->comment('1 for pending, 2 for clear'); // Status: 1 for pending, 2 for clear
            $table->integer('user_id')->nullable(); // Foreign key to users table (optional)
            $table->string('added_by')->nullable(); // User who added the record
            $table->string('updated_by')->nullable(); // User who last updated the record
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_dress_attributes');
    }
};
