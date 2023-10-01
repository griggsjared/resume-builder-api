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
        Schema::create('education', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subject_id')->index()->nullable();
            $table->string('name');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('major_degree')->nullable();
            $table->boolean('earned_major_degree')->default(false);
            $table->string('minor_degree')->nullable();
            $table->boolean('earned_minor_degree')->default(false);
            $table->timestamps();
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education');
    }
};
