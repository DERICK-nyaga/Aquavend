<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL enums can't be altered via the schema builder directly — raw SQL needed.
        DB::statement("ALTER TABLE notifications_log MODIFY channel ENUM('sms', 'whatsapp', 'email', 'push') NOT NULL");

        Schema::table('notifications_log', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('notifications_log', function (Blueprint $table) {
            $table->dropColumn('read_at');
        });

        DB::statement("ALTER TABLE notifications_log MODIFY channel ENUM('sms', 'whatsapp', 'email') NOT NULL");
    }
};