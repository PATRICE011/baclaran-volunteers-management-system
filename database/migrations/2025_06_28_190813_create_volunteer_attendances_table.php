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
        Schema::create('volunteer_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_id')->constrained()->onDelete('cascade');
            $table->foreignId('ministry_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('total_service_hours')->default(0);
            $table->unsignedInteger('meeting_attendance_count')->default(0);
            $table->unsignedInteger('absent_count')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('volunteer_id');
            $table->index('ministry_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_attendances');
    }
};
