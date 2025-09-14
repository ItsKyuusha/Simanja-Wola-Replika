<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userPegawai = auth()->user()->pegawai;
        if (!$userPegawai) {
            abort(403, 'Anda tidak memiliki data pegawai.');
        }

        $teamIds = $userPegawai->teams->pluck('id')->toArray();
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $search = trim((string) $request->input('search', ''));

        $namaBulan = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];

        $labelBulanTahun = match (true) {
            $bulan && $tahun   => strtoupper($namaBulan[(int)$bulan])." $tahun",
            $bulan && !$tahun  => strtoupper($namaBulan[(int)$bulan])." - Semua Tahun",
            !$bulan && $tahun  => "Semua Bulan - $tahun",
            default            => 'Semua Bulan & Tahun'
        };

        $members = Pegawai::whereHas('teams', fn($q) => $q->whereIn('teams.id', $teamIds))->get();
        $memberIds = $members->pluck('id')->toArray();

        // Query tugas
        $tasksQuery = Tugas::with(['pegawai.teams','jenisPekerjaan.team','semuaRealisasi'])
            ->whereIn('pegawai_id', $memberIds)
            ->where('asal', $userPegawai->nama)
            ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
            ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun));

        // Filter search
        if ($search !== '') {
            $keywords = preg_split('/[\s,]+/', $search, -1, PREG_SPLIT_NO_EMPTY);
            $tasksQuery->where(fn($q) => 
                collect($keywords)->each(fn($word) =>
                    $q->orWhereHas('pegawai', fn($qq) => $qq->where('nama','like',"%$word%"))
                      ->orWhereHas('jenisPekerjaan', fn($qq) => $qq->where('nama_pekerjaan','like',"%$word%"))
                )
            );
        }

        $tasks = $tasksQuery->get();

        // Transform tugas
        $tasks->transform(function($t){
            $approved = $t->semuaRealisasi->where('is_approved', true);
            $totalRealisasi = $approved->sum('realisasi');
            $progress = $t->target>0 ? min($totalRealisasi/$t->target,1) : 0;
            $bobot = $t->jenisPekerjaan->bobot ?? 0;

            $lastDate = $approved->max('tanggal_realisasi');
            $hariTelat = ($lastDate && $t->deadline && Carbon::parse($lastDate)->gt(Carbon::parse($t->deadline)))
                         ? Carbon::parse($lastDate)->diffInDays(Carbon::parse($t->deadline))
                         : 0;

            $penalti = $bobot * 0.1 * $hariTelat;
            $nilaiAkhir = max(0, ($bobot * $progress) - $penalti);

            $status = match (true) {
                $approved->isEmpty() => ($t->semuaRealisasi->isNotEmpty() ? 'Menunggu Persetujuan' : 'Belum Dikerjakan'),
                $totalRealisasi<$t->target => 'Ongoing',
                default => 'Selesai Dikerjakan'
            };

            $t->namaTim = $t->jenisPekerjaan->team->nama_tim ?? '-';
            $t->bobot = $bobot;
            $t->hariTelat = $hariTelat;
            $t->nilaiAkhir = round($nilaiAkhir,2);
            $t->status = $status;
            $t->totalTarget = $t->target ?? 0;
            $t->totalRealisasi = $totalRealisasi ?? 0;

            return $t;
        });

        $totalTugas = $tasks->count();
        $tugasSelesai = $tasks->where('status','Selesai Dikerjakan')->count();
        $tugasOngoing = $tasks->where('status','Ongoing')->count();
        $tugasBelum = $tasks->where('status','Belum Dikerjakan')->count();
        $rataNilaiAkhir = $totalTugas>0 ? round($tasks->avg('nilaiAkhir'),2) : 0;

        $grafikLabels = $tasks->pluck('jenisPekerjaan.nama_pekerjaan')->toArray();
        $grafikTarget = $tasks->pluck('totalTarget')->toArray();
        $grafikRealisasi = $tasks->pluck('totalRealisasi')->toArray();

        return view('admin.dashboard', compact(
            'members','tasks','totalTugas','tugasSelesai',
            'tugasOngoing','tugasBelum','rataNilaiAkhir',
            'grafikLabels','grafikTarget','grafikRealisasi','labelBulanTahun'
        ));
    }

    public function exportExcel(Request $request)
{
    $bulan = $request->input('bulan');
    $tahun = $request->input('tahun');
    $search = trim((string) $request->input('search',''));

    $userPegawai = auth()->user()->pegawai;

    if (!$userPegawai) {
        abort(403, 'Anda tidak memiliki data pegawai.');
    }

    $teamIds = $userPegawai->teams->pluck('id')->toArray();
    $memberIds = Pegawai::whereHas('teams', fn($q)=>$q->whereIn('teams.id', $teamIds))->pluck('id')->toArray();

    // ambil tugas
    $tasks = Tugas::with(['pegawai.teams','jenisPekerjaan.team','semuaRealisasi'])
        ->whereIn('pegawai_id', $memberIds)
        ->where('asal', $userPegawai->nama);

    if ($bulan) $tasks->whereMonth('created_at', $bulan);
    if ($tahun) $tasks->whereYear('created_at', $tahun);

    if ($search) {
        $keywords = preg_split('/[\s,]+/', $search, -1, PREG_SPLIT_NO_EMPTY);
        $tasks->where(function($q) use ($keywords) {
            foreach ($keywords as $word) {
                $q->orWhereHas('pegawai', fn($qq)=>$qq->where('nama','like',"%$word%"))
                  ->orWhereHas('jenisPekerjaan', fn($qq)=>$qq->where('nama_pekerjaan','like',"%$word%"));
            }
        });
    }

    $tasks = $tasks->get();

    // hitung total realisasi & nilai lainnya
    $tasks->transform(function ($t) {
        $approvedRealisasi = $t->semuaRealisasi->where('is_approved', true);
        $totalRealisasi = $approvedRealisasi->sum('realisasi');
        $t->totalRealisasi = $totalRealisasi;

        $t->bobot = $t->jenisPekerjaan->bobot ?? 0;

        $lastDate = $approvedRealisasi->max('tanggal_realisasi');
        $t->hariTelat = ($lastDate && $t->deadline) ? Carbon::parse($lastDate)->diffInDays(Carbon::parse($t->deadline)) : 0;

        $progress = $t->target>0 ? min($totalRealisasi/$t->target,1) : 0;
        $penalti = $t->bobot*0.1*$t->hariTelat;
        $t->nilaiAkhir = round(max(0,($t->bobot*$progress)-$penalti),2);

        $t->status = $totalRealisasi >= $t->target ? 'Selesai Dikerjakan' : ($totalRealisasi>0 ? 'Ongoing' : 'Belum Dikerjakan');

        $t->namaTim = $t->jenisPekerjaan->team->nama_tim ?? '-';

        return $t;
    });

    // buat array untuk excel
    $rows = $tasks->map(function($t,$i){
    return [
        'No' => $i+1,
        'Nama Pegawai' => $t->pegawai->nama ?? '-',
        'Nama Tim' => $t->namaTim,
        'Tugas' => $t->jenisPekerjaan->nama_pekerjaan ?? '-',
        'Target' => $t->target,
        'Realisasi' => $t->totalRealisasi ?? 0,
        'Histori Perubahan' => $t->semuaRealisasi->map(function($r){
            return \Carbon\Carbon::parse($r->tanggal_realisasi)->format('d M Y').':'.$r->realisasi;
        })->implode('; '),
        'Bobot' => $t->bobot,
        'Hari Telat' => $t->hariTelat,
        'Nilai Akhir' => $t->nilaiAkhir,
    ];
});


    return Excel::download(new class($rows) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
        private $rows;
        public function __construct($rows){ $this->rows=$rows; }
        public function collection(){ return new Collection($this->rows);}
        public function headings(): array{ return array_keys($this->rows->first() ?? []);}
    }, 'laporan_dashboard_admin.xlsx');
}
}