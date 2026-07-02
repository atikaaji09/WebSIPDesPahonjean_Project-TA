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
        Schema::create('rpjmdes_periode', function (Blueprint $table) {
            $table->id();

            $table->year('tahun_mulai');
            $table->year('tahun_selesai');

            $table->string('nama_periode')->nullable();
            $table->boolean('is_ditetapkan')->default(false);

            // 🔥 TAMBAHAN INI
            $table->unique(['tahun_mulai', 'tahun_selesai']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpjmdes_periode');
    }
};
