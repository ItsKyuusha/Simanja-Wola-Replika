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
        Schema::create('pekerjaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('tugas');
            $table->decimal('bobot', 5, 2);
            $table->string('asal');
            $table->integer('target');
            $table->integer('realisasi')->nullable();
            $table->string('satuan');
            $table->date('deadline');
            $table->text('catatan')->nullable();
            $table->date('tanggal_realisasi')->nullable();
            $table->string('file')->nullable();
            $table->integer('nilai_kualitas')->nullable();
            $table->integer('nilai_kuantitas')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
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
