<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('monitoring_logs', 'nama_website')) {
            Schema::table('monitoring_logs', function (Blueprint $table) {
                $table->string('nama_website')->nullable()->after('website_id');
            });
        }

    }

    public function down(): void
    {
        if (Schema::hasColumn('monitoring_logs', 'nama_website')) {
            Schema::table('monitoring_logs', function (Blueprint $table) {
                $table->dropColumn('nama_website');
            });
        }
    }
};
