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
        Schema::table('booking_dress_attributes', function (Blueprint $table) {
            $table->text('fault_notes')->nullable(); // Add fault_notes column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_dress_attributes', function (Blueprint $table) {
            $table->dropColumn('fault_notes'); // Drop fault_notes column if rollback happens
        });
    }
};
