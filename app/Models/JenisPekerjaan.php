<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisPekerjaan extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'satuan', 'bobot', 'pemberi_pekerjaan'];
}


