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
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jenis_pekerjaan_id');
            $table->unsignedBigInteger('pegawai_id'); // penerima tugas
            $table->integer('target');
            $table->string('asal')->nullable(); // asal input / instruksi
            $table->string('satuan');
            $table->date('deadline');
            $table->timestamps();

            $table->foreign('jenis_pekerjaan_id')->references('id')->on('jenis_pekerjaans')->onDelete('cascade');
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->onDelete('cascade');
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
