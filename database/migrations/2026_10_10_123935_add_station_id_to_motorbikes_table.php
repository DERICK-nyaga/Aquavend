<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motorbikes', function (Blueprint $table) {
            $table->foreignId('station_id')->nullable()->constrained('stations')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('motorbikes', function (Blueprint $table) {
            $table->dropForeign(['station_id']);
            $table->dropColumn('station_id');
        });
    }
};
