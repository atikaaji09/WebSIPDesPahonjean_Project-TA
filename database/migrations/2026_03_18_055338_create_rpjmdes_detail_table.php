<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rpjmdes_detail', function (Blueprint $table) {
            $table->id();

            // periode
            $table->foreignId('rpjmdes_id')
                ->nullable()
                ->constrained('rpjmdes_periode')
                ->nullOnDelete();

            // 🔥 WAJIB dari usulan
            $table->foreignId('usulan_id')
                ->constrained('usulan')
                ->onDelete('cascade');

            // mapping admin
            $table->foreignId('bidang_id')
                ->nullable()
                ->constrained('bidang')
                ->nullOnDelete();

            $table->foreignId('sub_bidang_id')
                ->nullable()
                ->constrained('sub_bidang')
                ->nullOnDelete();

            $table->foreignId('kegiatan_id')
                ->nullable()
                ->constrained('kegiatan')
                ->nullOnDelete();

            // pelengkap
            $table->string('sasaran_manfaat')->nullable();
            $table->json('tahun_pelaksanaan')->nullable();

            $table->decimal('anggaran', 15, 2)->nullable();
            $table->string('sumber')->nullable();
            $table->string('pola_pelaksanaan')->nullable();

            // monitoring
            $table->enum('status', ['baru', 'masuk_rkpdes', 'lanjutan'])
                ->default('baru');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rpjmdes_detail');
    }
};
