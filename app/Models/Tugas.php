<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $fillable = ['nama_tugas', 'jenis_pekerjaan_id', 'pegawai_id', 'target', 'asal', 'satuan', 'deadline'];

    public function jenisPekerjaan()
    {
        return $this->belongsTo(JenisPekerjaan::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function realisasi()
    {
        return $this->hasOne(RealisasiTugas::class);
    }
}
