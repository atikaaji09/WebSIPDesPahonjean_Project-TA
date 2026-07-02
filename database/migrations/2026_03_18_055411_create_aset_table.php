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
        Schema::create('aset', function (Blueprint $table) {
            $table->id();

            $table->string('klas_aset')->nullable();
            $table->string('nama_aset')->nullable();
            $table->string('jenis_kepemilikan')->nullable();
            $table->string('nomor_kepemilikan')->nullable();
            $table->date('tanggal_kepemilikan')->nullable();
            $table->string('kode_aset')->nullable();
            $table->integer('tahun_perolehan')->nullable();
            $table->decimal('nilai_perolehan', 20, 2)->nullable();
            $table->string('kondisi_aset')->nullable();
            $table->text('keterangan')->nullable();
            $table->decimal('luas', 10, 3)->nullable();
            $table->string('bukti_kepemilikan')->nullable();
            $table->string('titik_pangkal')->nullable();
            $table->string('titik_ujung')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset');
    }
};
