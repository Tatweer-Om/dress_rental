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
        Schema::create('maintgos', function (Blueprint $table) {
            $table->id();
            $table->string('dress_id');
            $table->string('maint_issue')->nullable();
            $table->longText('issue_notes')->nullable();
            $table->longText('maint_comp_notes')->nullable();
            $table->string('maint_cost')->nullable();
            $table->string('status')->nullable()->default(0)->comment('1 for under_maint, 2 for comp_maint ');
            $table->integer('user_id')->nullable();
            $table->string('added_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintgos');
    }
};
