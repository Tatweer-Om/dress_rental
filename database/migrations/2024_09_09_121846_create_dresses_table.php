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
        Schema::create('dresses', function (Blueprint $table) {
            $table->id();
            $table->integer('category_name')->nullable();
            $table->integer('brand_name')->nullable();
            $table->integer('color_name')->nullable();
            $table->integer('size_name')->nullable();
            $table->string('dress_name')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('price', 50, 3)->nullable();
            $table->integer('condition')->nullable()->comment('1 for new, 2 for used');
            $table->longText('notes')->nullable();
            $table->string('dress_image')->nullable();
            $table->string('added_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('user_id', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dresses');
    }
};
