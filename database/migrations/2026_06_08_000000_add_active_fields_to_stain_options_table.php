<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stain_options', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('sort_order');
            $table->string('inactive_reason', 500)->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('stain_options', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'inactive_reason']);
        });
    }
};
