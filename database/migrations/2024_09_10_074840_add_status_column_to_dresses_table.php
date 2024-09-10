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
        Schema::table('dresses', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dresses', function (Blueprint $table) {
            table->tinyInteger('status')->comment('1 = available, 2 = booked, 3 = reserved, 4 = laundry, 5 = maintenance')
            ->default(1); // Assuming default is available
        });
    }
};
