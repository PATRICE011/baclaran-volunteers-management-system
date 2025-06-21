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
        // basic info
        Schema::create('volunteers', function (Blueprint $table) {
            $table->id();
            $table->string('nickname')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('sex', ['Male', 'Female']);
            $table->string('address')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('email_address')->nullable();
            $table->string('occupation')->nullable();
            $table->enum('civil_status', ['Single', 'Married', 'Widow/er', 'Separated', 'Church', 'Civil', 'Others'])->nullable();
            $table->json('sacraments_received')->nullable(); // e.g. ["Baptism", "First Communion"]
            $table->json('formations_received')->nullable(); // e.g. ["BOS", "BFF"]
            $table->string('profile_picture')->nullable();
            $table->timestamps();
            // archive
            $table->boolean('is_archived')->default(false);
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('archived_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('archive_reason')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteers');
    }
};
