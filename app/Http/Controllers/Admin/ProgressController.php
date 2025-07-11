<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Tugas;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request)
{
    $teamId = auth()->user()->pegawai->team_id;
    $search = $request->input('search');

    $tugas = Tugas::with(['pegawai', 'realisasi'])
        ->whereHas('pegawai', function($q) use ($teamId, $search) {
            $q->where('team_id', $teamId);

            if ($search) {
                $q->where(function($qq) use ($search) {
                    $qq->where('nama', 'like', "%{$search}%")
                       ->orWhere('nip', 'like', "%{$search}%");
                });
            }
        })
        ->when($search, function($q) use ($search) {
            $q->orWhere('nama_tugas', 'like', "%{$search}%");
        })
        ->get();

    return view('admin.progress.index', compact('tugas'));
}

}

