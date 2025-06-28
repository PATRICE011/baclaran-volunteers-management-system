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
        Schema::create('ministries', function (Blueprint $table) {
            $table->id();
            $table->string('ministry_name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->enum('ministry_type', ['PARISH', 'PASTORAL', 'SOCIAL_MISSION', 'SUB_GROUP']);
            $table->timestamps();
            $table->softDeletes();

            // Add index on parent_id for better query performance
            $table->index('parent_id');

            // Optional: Add index on ministry_type for better query performance
            $table->index('ministry_type');

            // Foreign key constraint for parent-child relationship
            $table->foreign('parent_id')->references('id')->on('ministries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ministries');
    }
};
