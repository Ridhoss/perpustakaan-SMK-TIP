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
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('isbn');
            $table->string('pengarang');
            $table->string('judul');
            $table->bigInteger('eks');
            $table->integer('thn_inv');
            $table->bigInteger('asl_id')->nullable();
            $table->bigInteger('ktg_id')->nullable();
            $table->bigInteger('bhs_id')->nullable();
            $table->string('no_inv');
            $table->integer('tahun_terbit')->nullable();
            $table->text('sinopsis')->nullable();
            $table->string('photo')->nullable();
            $table->string('ket')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bukus');
    }
};
