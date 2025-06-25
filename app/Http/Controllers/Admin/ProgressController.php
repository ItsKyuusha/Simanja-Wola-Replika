<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Progress;
use App\Models\NilaiAkhirUser;

class ProgressController extends Controller
{
    public function index()
    {
        $progress = Progress::with('user.tim')->get();
        $nilaiAkhir = NilaiAkhirUser::with('user.tim')->get();

        return view('admin.progress', compact('progress', 'nilaiAkhir'));
    }
}


