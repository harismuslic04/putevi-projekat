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
        Schema::create('traffic_accidents', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->decimal('gps_lat', 10, 8);
            $table->decimal('gps_lng', 11, 8);
            $table->string('severity')->default('laka'); // laka, teska, fatalna
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traffic_accidents');
    }
};
