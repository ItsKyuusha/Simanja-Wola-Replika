<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'pegawai_id'];

    /**
     * Relasi ke Pegawai (1 user = 1 pegawai)
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /**
     * Relasi ke Team melalui Pegawai
     * (User → Pegawai → Teams)
     */
    public function teams()
    {
        return $this->pegawai
            ? $this->pegawai->teams()
            : $this->hasManyThrough(Team::class, Pegawai::class, 'id', 'id');
    }
}
