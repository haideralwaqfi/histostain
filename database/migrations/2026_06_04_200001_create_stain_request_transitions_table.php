<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Append-only audit log. No updated_at; updates are prevented in model boot.
        Schema::create('stain_request_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stain_request_id')->constrained()->cascadeOnDelete();
            $table->string('from_status')->nullable(); // null on creation record
            $table->string('to_status');
            $table->foreignId('performed_by_id')->constrained('users');
            $table->text('note')->nullable();
            $table->timestamp('created_at');

            $table->index('stain_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stain_request_transitions');
    }
};
