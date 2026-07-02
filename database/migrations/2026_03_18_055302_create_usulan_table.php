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
        Schema::create('usulan', function (Blueprint $table) {
            $table->id();

            // nomor usulan (unik untuk tracking)
            $table->string('no_usulan')->unique();

            // data pengusul
            $table->string('nama_lengkap');

            // 🔥 relasi utama (cukup ini saja)
            $table->foreignId('rt_rw_id')
                ->constrained('rt_rw')
                ->onDelete('cascade')
                ->index();

            $table->foreignId('dusun_id')
                ->constrained('dusun')
                ->onDelete('cascade');

            // data usulan
            $table->text('gagasan_kegiatan');
            $table->string('lokasi');

            $table->string('volume');
            $table->string('satuan');

            // penerima manfaat
            $table->integer('penerima_laki')->default(0);
            $table->integer('penerima_perempuan')->default(0);
            $table->integer('penerima_rtm')->default(0);

            // status usulan (sesuai UI approve/reject)
            $table->enum('status', ['diajukan', 'diterima', 'ditolak', 'masuk_rpjmdes'])
                ->default('diajukan')
                ->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usulan');
    }
};
