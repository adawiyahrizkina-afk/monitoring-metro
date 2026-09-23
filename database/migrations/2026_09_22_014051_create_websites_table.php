<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id();

            $table->string('nama_website');
            $table->string('instansi');
            $table->text('url');

            $table->string('status')->default('Belum Dicek');

            $table->integer('response_time')->nullable();

            $table->boolean('monitoring_aktif')
                ->default(true);

            $table->timestamp('last_checked_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};