<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('delivery_staff_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->string('motorbike_registration')->nullable()->after('delivery_staff_id');
            $table->timestamp('dispatched_at')->nullable()->after('motorbike_registration');
            $table->timestamp('delivered_at')->nullable()->after('dispatched_at');
            $table->integer('delivery_time_minutes')->nullable()->after('delivered_at');
            $table->decimal('delivery_score', 5, 2)->nullable()->after('delivery_time_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
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
    }
};