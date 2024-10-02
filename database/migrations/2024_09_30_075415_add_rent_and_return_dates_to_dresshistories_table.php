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
        Schema::table('dress_histories', function (Blueprint $table) {
            $table->date('rent_date')->nullable()->after('type');   // Add rent_date column
            $table->date('return_date')->nullable()->after('rent_date');  // Add return_date column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dress_histories', function (Blueprint $table) {
            $table->dropColumn('rent_date');
            $table->dropColumn('return_date');
        });
    }
};
