<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorbikes', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('model')->nullable();
            $table->enum('status', ['available', 'assigned', 'maintenance'])->default('available');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('assigned_motorbike_id')->nullable()->constrained('motorbikes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['assigned_motorbike_id']);
            $table->dropColumn('assigned_motorbike_id');
        });

        Schema::dropIfExists('motorbikes');
    }
};