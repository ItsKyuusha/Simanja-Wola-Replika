<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = ['nama_tim'];

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }
}
