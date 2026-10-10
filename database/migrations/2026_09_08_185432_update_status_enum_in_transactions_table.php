<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For MySQL / MariaDB: Update ENUM options to include new status values
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'confirmed', 'dispatched', 'completed', 'refunded') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'completed', 'refunded') DEFAULT 'pending'");
    }
};