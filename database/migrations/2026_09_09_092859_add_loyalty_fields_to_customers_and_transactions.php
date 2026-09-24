<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->timestamp('last_loyalty_discount_at')->nullable()->after('phone');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('discount_amount', 10, 2)->default(0.00)->after('total_amount');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('last_loyalty_discount_at');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('discount_amount');
        });
    }
};