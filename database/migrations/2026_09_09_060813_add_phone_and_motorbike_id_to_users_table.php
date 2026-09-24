<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'assigned_motorbike_id')) {
                $table->foreignId('assigned_motorbike_id')
                    ->nullable()
                    ->after('role')
                    ->constrained('motorbikes')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'assigned_motorbike_id')) {
                $table->dropForeign(['assigned_motorbike_id']);
                $table->dropColumn('assigned_motorbike_id');
            }

            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};