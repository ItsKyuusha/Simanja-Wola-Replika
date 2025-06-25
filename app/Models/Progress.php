<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Progress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tugas', 'bobot', 'asal', 'target', 'realisasi',
        'satuan', 'deadline', 'tanggal_realisasi',
        'nilai_kualitas', 'nilai_kuantitas', 'keterangan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

