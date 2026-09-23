<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restock_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('station_id')->constrained()->onDelete('cascade');
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('liters_added', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->enum('status', ['pending', 'received', 'cancelled'])->default('pending');
            $table->timestamp('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restock_orders');
    }
};