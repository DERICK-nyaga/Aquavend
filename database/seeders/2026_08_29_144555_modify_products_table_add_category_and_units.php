<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('category', ['water', 'oil', 'milk', 'yoghurt', 'eggs', 'bottles'])
                ->default('water')
                ->after('name');

            $table->decimal('size_value', 8, 2)->nullable()->after('category');
            $table->enum('unit', ['ml', 'l', 'pcs', 'dozen', 'tray'])->default('l')->after('size_value');

            $table->renameColumn('volume_liters_legacy', 'volume_liters_legacy'); // keep old data, stop using it
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['category', 'size_value', 'unit']);
            $table->renameColumn('volume_liters', 'volume_liters_legacy');
        });
    }
};