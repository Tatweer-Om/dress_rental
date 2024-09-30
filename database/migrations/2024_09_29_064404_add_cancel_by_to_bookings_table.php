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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('cancel_by')->nullable()->after('status'); // Adds the column after 'status'
            $table->date('cancel_date')->nullable()->after('cancel_by'); // Adds the column after 'cancel_by'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drops the columns 'cancel_by' and 'cancel_date' when rolling back the migration
            $table->dropColumn(['cancel_by', 'cancel_date']);
        });
    }
};
