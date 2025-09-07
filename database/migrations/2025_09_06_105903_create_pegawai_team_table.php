<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawai_team', function (Blueprint $table) {
            // Relasi pegawai & tim
            $table->foreignId('pegawai_id')
                  ->constrained('pegawais')
                  ->cascadeOnDelete();

            $table->foreignId('team_id')
                  ->constrained('teams')
                  ->cascadeOnDelete();

            // Tandai apakah pegawai adalah ketua tim
            $table->boolean('is_leader')->default(false);

            $table->timestamps();

            // Composite primary key untuk mencegah duplikat
            $table->primary(['pegawai_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawai_team');
    }
};
