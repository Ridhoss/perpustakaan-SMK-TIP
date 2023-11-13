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
        Schema::create('detailpinjams', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->date('tgl_pinjam');
            $table->date('tgl_kembali');
            $table->bigInteger('id_petugas');
            $table->bigInteger('qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailpinjams');
    }
};
