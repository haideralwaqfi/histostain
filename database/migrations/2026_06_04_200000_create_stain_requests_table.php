<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stain_requests', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique(); // public-facing ID, non-sequential

            $table->foreignId('doctor_id')->constrained('users');
            $table->foreignId('assigned_tech_id')->nullable()->constrained('users');

            $table->string('type'); // StainRequestType enum
            $table->string('status')->default('pending'); // StainRequestStatus enum
            $table->string('priority')->default('routine'); // StainRequestPriority enum

            // Shared clinical fields
            $table->string('mrn')->nullable(); // patient MRN
            $table->string('lab_number')->nullable();
            $table->string('case_number');
            $table->text('notes')->nullable();

            // Type-specific structured data.
            // For block-based types: {"blocks": [{block_id, ...fields}]}
            // For cytology (slide-based): {slide_id, stain, indication}
            $table->json('type_data');

            $table->timestamps();

            $table->index(['status', 'priority']); // tech queue sort
            $table->index('doctor_id');
            $table->index('assigned_tech_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stain_requests');
    }
};
