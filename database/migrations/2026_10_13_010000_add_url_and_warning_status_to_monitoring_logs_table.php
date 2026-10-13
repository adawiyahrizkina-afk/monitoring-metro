<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('monitoring_logs', 'url')) {
            Schema::table('monitoring_logs', function (Blueprint $table) {
                $table->text('url')->nullable()->after('nama_website');
            });
        }

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
