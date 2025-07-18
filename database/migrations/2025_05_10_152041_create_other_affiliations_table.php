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
        // info sheet
        Schema::create('other_affiliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('volunteer_id')->constrained()->onDelete('cascade');
            $table->string('organization_name');
            $table->string('year_started')->nullable();
            $table->string('year_ended')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('other_affiliations');
    }
};
