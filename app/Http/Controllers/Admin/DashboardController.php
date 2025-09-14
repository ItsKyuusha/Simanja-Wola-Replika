<?php

// DashboardController
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userPegawai = auth()->user()->pegawai;

        if (!$userPegawai) {
            abort(403, 'Anda tidak memiliki data pegawai.');
        }

        // semua ID tim user (ketua)
        $teamIds = $userPegawai->teams->pluck('id')->toArray();

        // filter bulan & tahun
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $namaBulan = [
            1=>'Januari',2=>'Februari',3=>'Maret',
            4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',
            10=>'Oktober',11=>'November',12=>'Desember'
        ];

        $labelBulanTahun = match (true) {
            $bulan && $tahun   => strtoupper($namaBulan[(int)$bulan])." $tahun",
            $bulan && !$tahun  => strtoupper($namaBulan[(int)$bulan])." - Semua Tahun",
            !$bulan && $tahun  => "Semua Bulan - $tahun",
            default            => 'Semua Bulan & Tahun'
        };

        // ambil anggota tim
        $members = Pegawai::whereHas('teams', function ($q) use ($teamIds) {
            $q->whereIn('teams.id', $teamIds);
        })->get();
        $memberIds = $members->pluck('id')->toArray();

        // ambil tugas hanya dari ketua tim ini
        $tasksQuery = Tugas::with(['pegawai.teams','jenisPekerjaan.team','semuaRealisasi'])
            ->whereIn('pegawai_id', $memberIds)
            ->where('asal', $userPegawai->nama);

        if ($bulan) $tasksQuery->whereMonth('created_at',$bulan);
        if ($tahun) $tasksQuery->whereYear('created_at',$tahun);

        $tasks = $tasksQuery->get();

        // transform data tugas
        $tasks->transform(function ($t) {
            $approvedRealisasi = $t->semuaRealisasi->where('is_approved', true);
            $totalRealisasi = $approvedRealisasi->sum('realisasi');
            $progress = $t->target>0 ? min($totalRealisasi/$t->target,1) : 0;

            $bobot = $t->jenisPekerjaan->bobot ?? 0;

            $lastDate = $approvedRealisasi->max('tanggal_realisasi');
            $hariTelat=0;
            if ($lastDate && $t->deadline && Carbon::parse($lastDate)->gt(Carbon::parse($t->deadline))) {
                $hariTelat = Carbon::parse($lastDate)->diffInDays(Carbon::parse($t->deadline));
            }

            $penalti = $bobot*0.1*$hariTelat;
            $nilaiAkhir = max(0,($bobot*$progress)-$penalti);

            $status = match (true) {
                $approvedRealisasi->isEmpty() => ($t->semuaRealisasi->isNotEmpty()?'Menunggu Persetujuan':'Belum Dikerjakan'),
                $totalRealisasi<$t->target    => 'Ongoing',
                default                       => 'Selesai Dikerjakan'
            };

            // nama tim: langsung ambil dari jenis pekerjaan (tim pemberi tugas)
            $t->namaTim = $t->jenisPekerjaan->team->nama_tim ?? '-';

            $t->bobot=$bobot;
            $t->hariTelat=$hariTelat;
            $t->nilaiAkhir=round($nilaiAkhir,2);
            $t->status=$status;
            $t->totalTarget=$t->target??0;
            $t->totalRealisasi=$totalRealisasi??0;

            return $t;
        });

        $totalTugas = $tasks->count();
        $tugasSelesai=$tasks->where('status','Selesai Dikerjakan')->count();
        $tugasOngoing=$tasks->where('status','Ongoing')->count();
        $tugasBelum=$tasks->where('status','Belum Dikerjakan')->count();

        $rataNilaiAkhir=$totalTugas>0?round($tasks->avg('nilaiAkhir'),2):0;

        $grafikLabels=$tasks->pluck('jenisPekerjaan.nama_pekerjaan')->toArray();
        $grafikTarget=$tasks->pluck('totalTarget')->toArray();
        $grafikRealisasi=$tasks->pluck('totalRealisasi')->toArray();

        return view('admin.dashboard',compact(
            'members','tasks','totalTugas','tugasSelesai',
            'tugasOngoing','tugasBelum','rataNilaiAkhir',
            'grafikLabels','grafikTarget','grafikRealisasi','labelBulanTahun'
        ));
    }
}
