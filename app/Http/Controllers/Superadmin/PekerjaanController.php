<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Pekerjaan;

class PekerjaanController extends Controller
{
    public function index()
    {
        $pekerjaan = Pekerjaan::with('user.tim')->get();
        return view('superadmin.pekerjaan', compact('pekerjaan'));
    }
}


