<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;

class PegawaiController extends Controller
{
    public function index()
    {
        $teamId = auth()->user()->pegawai->team_id;

        $pegawai = Pegawai::where('team_id', $teamId)->get();

        return view('admin.pegawai.index', compact('pegawai'));
    }
}

