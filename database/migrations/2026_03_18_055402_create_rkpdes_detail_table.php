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
        Schema::create('rkpdes_detail', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rkpdes_id')
                ->constrained('rkpdes')
                ->cascadeOnDelete();

            $table->foreignId('rpjmdes_detail_id')
                ->nullable()
                ->constrained('rpjmdes_detail')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('bidang_id')->nullable()->constrained('bidang')->nullOnDelete();
            $table->foreignId('sub_bidang_id')->nullable()->constrained('sub_bidang')->nullOnDelete();
            $table->foreignId('kegiatan_id')->nullable()->constrained('kegiatan')->nullOnDelete();

            $table->string('sdgs')->nullable();
            $table->string('data_existing')->nullable();
            $table->string('target_capaian')->nullable();
            $table->decimal('volume', 10, 2)->nullable();
            $table->string('satuan')->nullable();

            $table->string('lokasi')->nullable();
            $table->decimal('anggaran', 15, 2)->nullable();
            $table->string('sumber_dana')->nullable();

            $table->integer('penerima_laki')->default(0);
            $table->integer('penerima_perempuan')->default(0);
            $table->integer('penerima_rtm')->default(0);

            $table->string('waktu_pelaksanaan')->nullable();
            $table->string('pelaksana_kegiatan')->nullable();
            $table->string('rencana_tpk')->nullable();
            $table->string('pola_pelaksanaan')->nullable();

            $table->enum('status', ['draft', 'diajukan', 'ditolak', 'disetujui'])
                ->default('draft');

            $table->enum('status_progres', ['baru', 'diproses', 'lanjutan', 'selesai'])
                ->default('baru');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rkpdes_detail');
    }
};
