<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update status ENUM options
        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'confirmed', 'dispatched', 'completed', 'refunded') DEFAULT 'pending'");

        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'delivery_staff_id')) {
                $table->foreignId('delivery_staff_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('motorbike_registration')->nullable();
                $table->timestamp('dispatched_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->integer('delivery_time_minutes')->nullable();
                $table->decimal('delivery_score', 5, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['delivery_staff_id']);
            $table->dropColumn([
                'delivery_staff_id',
                'motorbike_registration',
                'dispatched_at',
                'delivered_at',
                'delivery_time_minutes',
                'delivery_score',
            ]);
        });

        DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('pending', 'completed', 'refunded') DEFAULT 'pending'");
    }
};