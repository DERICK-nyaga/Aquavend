<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE station_product_stock ADD CONSTRAINT chk_quantity_non_negative CHECK (quantity_available >= 0)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE station_product_stock DROP CONSTRAINT chk_quantity_non_negative');
    }
};