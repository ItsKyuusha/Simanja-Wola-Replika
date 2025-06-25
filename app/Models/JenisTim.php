<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisTim extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];

    // Tim memiliki banyak user
    public function users()
    {
        return $this->hasMany(User::class, 'tim_id');
    }
}

