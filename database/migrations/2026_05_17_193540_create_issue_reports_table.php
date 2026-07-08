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
        Schema::create('issue_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained('infrastructure_assets')->nullOnDelete();
            $table->foreignId('road_segment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('duplicate_of_id')->nullable()->constrained('issue_reports')->nullOnDelete();
            $table->text('description');
            $table->string('type'); // rupa, znak, sneg
            $table->decimal('gps_lat', 10, 8);
            $table->decimal('gps_lng', 11, 8);
            $table->string('status')->default('prijavljeno'); // prijavljeno, verifikovano, nalog_izdat, sanirano, odbijeno, duplikat
            $table->string('image_path')->nullable();
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_reports');
    }
};
