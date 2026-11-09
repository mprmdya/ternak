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
        Schema::create('sensor_logs', function (Blueprint $table) {
            $table->id();
            $table->float('nh3')->default(0);
            $table->float('h2s')->default(0);
            $table->float('temp')->default(0);
            $table->float('hum')->default(0);
            $table->float('press')->default(0);
            $table->integer('pm1')->default(0);
            $table->integer('pm25')->default(0);
            $table->integer('pm10')->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_logs');
    }
};
