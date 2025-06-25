<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NilaiAkhirUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'kategori_bobot', 'total_bobot', 'nilai_akhir'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


