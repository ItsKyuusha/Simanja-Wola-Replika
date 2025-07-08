<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'pegawai_id'];

    // Relasi ke Pegawai (jika user terhubung dengan pegawai)
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
