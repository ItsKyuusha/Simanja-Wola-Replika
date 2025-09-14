<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $fillable = ['pegawai_id', 'nilai_akhir'];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
