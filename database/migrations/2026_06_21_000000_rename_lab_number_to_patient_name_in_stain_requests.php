<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stain_requests', function (Blueprint $table) {
            $table->renameColumn('lab_number', 'patient_name');
        });
    }

    public function down(): void
    {
        Schema::table('stain_requests', function (Blueprint $table) {
            $table->renameColumn('patient_name', 'lab_number');
        });
    }
};
