<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE monitoring_logs MODIFY status ENUM('Online', 'Offline', 'Warning') NOT NULL"
        );
    }

    public function down(): void
    {
        DB::statement(
            "UPDATE monitoring_logs SET status = 'Online' WHERE status = 'Warning'"
        );

        DB::statement(
            "ALTER TABLE monitoring_logs MODIFY status ENUM('Online', 'Offline') NOT NULL"
        );
    }
};
