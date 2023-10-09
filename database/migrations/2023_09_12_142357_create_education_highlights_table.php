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
        Schema::create('education_highlights', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('education_id')->index()->nullable();
            $table->string('content');
            $table->integer('sort')->default(9999);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_highlights');
    }
};
