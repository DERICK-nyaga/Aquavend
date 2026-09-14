<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE stations ADD CONSTRAINT chk_current_level_non_negative CHECK (current_level_liters >= 0)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE stations DROP CONSTRAINT chk_current_level_non_negative');
    }
};