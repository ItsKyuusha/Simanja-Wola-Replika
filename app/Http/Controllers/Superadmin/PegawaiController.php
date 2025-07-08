<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;

class PegawaiController extends Controller
{
    public function index()
    {
        $data = Pegawai::with('team')->get();
        return view('superadmin.master_pegawai.index', compact('data'));
    }
}
