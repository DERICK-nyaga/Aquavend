<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remap any existing roles that are not 'admin', 'director', or 'agent' to 'agent' first
        DB::table('users')
            ->whereNotIn('role', ['admin', 'director', 'agent'])
            ->update(['role' => 'agent']);

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','director','agent') DEFAULT 'agent'");

        Schema::table('users', function (Blueprint $table) {
            $table->enum('employment_status', ['pending_approval', 'active', 'suspended', 'dismissed', 'on_leave'])
                ->default('pending_approval')->after('role');
            $table->foreignId('approved_by')->nullable()->after('employment_status')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('status_reason')->nullable()->after('approved_at'); // reason for suspension/dismissal
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['employment_status', 'approved_by', 'approved_at', 'status_reason']);
        });

        // Remap 'director' back to 'agent' before restoring the old enum constraint
        DB::table('users')
            ->whereNotIn('role', ['admin', 'agent'])
            ->update(['role' => 'agent']);

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','agent') DEFAULT 'agent'");
    }
};