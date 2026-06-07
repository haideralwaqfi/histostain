<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stain_options', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);          // 'ihc' | 'special_stain'
            $table->string('key', 100);
            $table->string('label', 200);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['type', 'key']);
            $table->index(['type', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stain_options');
    }
};
