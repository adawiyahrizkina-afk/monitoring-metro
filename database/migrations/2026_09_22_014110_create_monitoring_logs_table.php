<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('website_id')
                ->constrained('websites')
                ->cascadeOnDelete();

            $table->enum('status', [
                'Online',
                'Offline'
            ]);

            $table->integer('response_time')->nullable();

            $table->timestamp('checked_at');

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring_logs');
    }
};