<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update any NULL or unrecognized roles to 'user' (or 'customer')
        DB::statement("
            UPDATE users 
            SET role = 'user' 
            WHERE role NOT IN ('admin', 'staff', 'user', 'customer', 'agent') OR role IS NULL
        ");

        // 2. Modify the column to include 'staff'
        DB::statement("
            ALTER TABLE users 
            MODIFY COLUMN role ENUM('admin', 'staff', 'user', 'customer', 'agent') NOT NULL DEFAULT 'staff'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE users 
            MODIFY COLUMN role ENUM('admin', 'staff', 'user', 'customer', 'agent') NOT NULL DEFAULT 'staff'
        ");
    }
};