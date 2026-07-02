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
        Schema::create('monitoring_rkpdes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rkpdes_detail_id')
                ->constrained('rkpdes_detail')
                ->onDelete('cascade');

            $table->integer('volume_realisasi')->default(0);

            $table->date('tanggal')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoring_rkpdes');
    }
};
