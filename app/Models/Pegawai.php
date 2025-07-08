<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $fillable = ['nama', 'nip', 'jabatan', 'team_id'];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    public function progress()
    {
        return $this->hasOne(Progress::class);
    }
}
