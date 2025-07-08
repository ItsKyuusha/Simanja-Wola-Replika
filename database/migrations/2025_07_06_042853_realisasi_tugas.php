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
        Schema::create('realisasi_tugas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tugas_id');
            $table->integer('realisasi');
            $table->date('tanggal_realisasi');
            $table->text('catatan')->nullable();
            $table->string('file_bukti')->nullable();
            $table->integer('nilai_kualitas')->nullable();
            $table->integer('nilai_kuantitas')->nullable();
            $table->timestamps();

            $table->foreign('tugas_id')->references('id')->on('tugas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
