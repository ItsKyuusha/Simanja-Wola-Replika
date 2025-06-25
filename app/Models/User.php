<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama', 'nip', 'tim_id', 'jabatan', 'email', 'password', 'role'
    ];

    protected $hidden = ['password'];

    // Relasi ke tim
    public function tim()
    {
        return $this->belongsTo(JenisTim::class, 'tim_id');
    }

    // Pekerjaan yang dimiliki user
    public function pekerjaan()
    {
        return $this->hasMany(Pekerjaan::class);
    }

    // Progress kinerja user
    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    // Nilai akhir kinerja user
    public function nilaiAkhir()
    {
        return $this->hasOne(NilaiAkhirUser::class);
    }
}
