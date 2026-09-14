<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('station_product_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_available', 10, 2)->default(0);
            $table->decimal('low_stock_threshold', 10, 2)->default(5);
            $table->timestamps();

            $table->unique(['station_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_product_stock');
    }
};