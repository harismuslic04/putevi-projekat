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
        Schema::create('infrastructure_assets', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Znak, Semafor, Most...
            $table->decimal('gps_lat', 10, 8);
            $table->decimal('gps_lng', 11, 8);
            $table->string('status')->default('active'); // active, damaged, repairing
            $table->json('properties_json')->nullable();
            $table->date('installed_at')->nullable();
            $table->date('warranty_until')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infrastructure_assets');
    }
};
