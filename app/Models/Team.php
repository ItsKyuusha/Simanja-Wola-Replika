<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['nama_tim'];

    /**
     * Relasi ke Pegawai (Many-to-Many via pegawai_team)
     */
    public function pegawais()
    {
        return $this->belongsToMany(Pegawai::class, 'pegawai_team', 'team_id', 'pegawai_id')
                    ->withPivot('is_leader')
                    ->withTimestamps();
    }
}
