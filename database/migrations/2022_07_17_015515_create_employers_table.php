<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employers', function (Blueprint $table) {
            $table->id();
            $table->integer('subject_id')->unsigned()->index();
            $table->string('name');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->boolean('is_current')->default(0);
            $table->timestamps();
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employers');
    }
};
