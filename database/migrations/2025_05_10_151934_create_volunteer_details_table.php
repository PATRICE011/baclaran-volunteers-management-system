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
        //info sheet 
        Schema::create('volunteer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_id')->constrained()->onDelete('cascade');
            $table->foreignId('ministry_id')->nullable()->constrained('ministries')->onDelete('set null');
            $table->string('line_group')->nullable(); // RMM, RYM, RCCOM etc
            $table->string('applied_month_year')->nullable();
            $table->string('regular_years_month')->nullable();
            $table->string('full_name');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_details');
    }
};
