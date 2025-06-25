<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pekerjaan;

class PekerjaanController extends Controller
{
    public function index()
    {
        $pekerjaan = Pekerjaan::with('user.tim')->get();
        return view('admin.pekerjaan', compact('pekerjaan'));
    }
}


