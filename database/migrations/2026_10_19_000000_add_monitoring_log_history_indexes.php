<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitoring_logs', function (Blueprint $table) {
            $table->index(['status', 'checked_at'], 'monitoring_logs_status_checked_at_index');
            $table->index(
                ['website_id', 'status', 'checked_at'],
                'monitoring_logs_website_status_checked_at_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('monitoring_logs', function (Blueprint $table) {
            $table->dropIndex('monitoring_logs_status_checked_at_index');
            $table->dropIndex('monitoring_logs_website_status_checked_at_index');
        });
    }
};
