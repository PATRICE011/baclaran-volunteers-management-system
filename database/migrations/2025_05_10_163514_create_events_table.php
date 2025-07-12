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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('description')->nullable();
            $table->foreignId('ministry_id')->nullable()->constrained()->onDelete('set null');
              // archive fields
            $table->boolean('is_archived')->default(false);
            $table->dateTime('archived_at')->nullable();  // Add the column properly
            $table->foreignId('archived_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('archive_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
