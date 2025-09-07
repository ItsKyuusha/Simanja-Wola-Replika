<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\JenisPekerjaan;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class PekerjaanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = auth()->user();
        $pegawai = $user->pegawai;

        $tugas = Tugas::with(['pegawai', 'jenisPekerjaan', 'realisasi'])
            ->where(function ($query) use ($pegawai, $user) {
                // filter berdasarkan asal sesuai pegawai / user login
                $query->where('asal', $pegawai->nama ?? $user->name);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_tugas', 'like', "%{$search}%")
                        ->orWhereHas('pegawai', function ($q2) use ($search) {
                            $q2->where('nama', 'like', "%{$search}%")
                                ->orWhere('nip', 'like', "%{$search}%");
                        });
                });
            })
            ->get();

        $pegawaiList = Pegawai::all();
        $jenisPekerjaan = JenisPekerjaan::all();

        return view('admin.pekerjaan.index', [
            'tugas' => $tugas,
            'pegawai' => $pegawaiList,
            'jenisPekerjaan' => $jenisPekerjaan
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama_tugas'         => 'required',
            'pegawai_id'         => 'required|exists:pegawais,id',
            'jenis_pekerjaan_id' => 'required|exists:jenis_pekerjaans,id',
            'target'             => 'required|numeric',
            'satuan'             => 'required|string',
            'deadline'           => 'required|date',
        ]);

        // nama pemberi (pegawai login / user)
        $pemberi = auth()->user()->pegawai->nama
            ?? auth()->user()->name
            ?? 'Tidak diketahui';

        Tugas::create([
            'nama_tugas'         => $request->nama_tugas,
            'pegawai_id'         => $request->pegawai_id,
            'jenis_pekerjaan_id' => $request->jenis_pekerjaan_id,
            'target'             => $request->target,
            'satuan'             => $request->satuan,
            'asal'               => $pemberi,
            'deadline'           => $request->deadline,
        ]);

        return redirect()->route('admin.pekerjaan.index')->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tugas'         => 'required',
            'pegawai_id'         => 'required|exists:pegawais,id',
            'jenis_pekerjaan_id' => 'required|exists:jenis_pekerjaans,id',
            'target'             => 'required|numeric',
            'satuan'             => 'required|string',
            'deadline'           => 'required|date',
        ]);

        $tugas = Tugas::findOrFail($id);

        // kalau user punya banyak tim, ambil nama semua tim → join pakai koma
        $asalInstruksi = auth()->user()->teams->pluck('nama_tim')->join(', ') ?: 'Tidak diketahui';

        $tugas->update([
            'nama_tugas'         => $request->nama_tugas,
            'pegawai_id'         => $request->pegawai_id,
            'jenis_pekerjaan_id' => $request->jenis_pekerjaan_id,
            'target'             => $request->target,
            'satuan'             => $request->satuan,
            'asal'               => $asalInstruksi,
            'deadline'           => $request->deadline,
        ]);

        return redirect()->route('admin.pekerjaan.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);
        $tugas->delete();

        return redirect()->route('admin.pekerjaan.index')->with('success', 'Tugas berhasil dihapus.');
    }

    public function export()
    {
        $teamIds = auth()->user()->teams->pluck('id');

        return Excel::download(new class($teamIds) implements FromCollection, WithHeadings, WithStyles {
            protected $teamIds;
            public function __construct($teamIds)
            {
                $this->teamIds = $teamIds;
            }

            public function collection()
            {
                return Tugas::with(['pegawai.teams', 'jenisPekerjaan'])
                    ->whereHas('pegawai.teams', function ($q) {
                        $q->whereIn('teams.id', $this->teamIds);
                    })
                    ->get()
                    ->map(function ($tugas, $index) {
                        return [
                            'No'              => $index + 1,
                            'Nama Tugas'      => $tugas->nama_tugas,
                            'Pegawai'         => $tugas->pegawai->nama ?? '-',
                            'Jenis Pekerjaan' => $tugas->jenisPekerjaan->nama_pekerjaan ?? '-',
                            'Target'          => $tugas->target,
                            'Satuan'          => $tugas->satuan,
                            'Asal Instruksi'  => $tugas->asal,
                            'Deadline'        => $tugas->deadline ? Carbon::parse($tugas->deadline)->format('d-m-Y') : '-',
                        ];
                    });
            }

            public function headings(): array
            {
                return [
                    'No',
                    'Nama Tugas',
                    'Pegawai',
                    'Jenis Pekerjaan',
                    'Target',
                    'Satuan',
                    'Asal Instruksi',
                    'Deadline',
                ];
            }

            public function styles(Worksheet $sheet)
            {
                $highestRow    = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Header
                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);

                // Data
                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'left'],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
                    ]
                ]);

                // Kolom "No" rata tengah
                $sheet->getStyle('A2:A' . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                ]);

                return [];
            }
        }, 'tugas.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $teamIds = auth()->user()->teams->pluck('id');

        Excel::import(new class($teamIds) implements \Maatwebsite\Excel\Concerns\ToModel, \Maatwebsite\Excel\Concerns\WithHeadingRow {
            protected $teamIds;
            public function __construct($teamIds)
            {
                $this->teamIds = $teamIds;
            }

            public function model(array $row)
            {
                $deadline = null;
                if (!empty($row['deadline'])) {
                    try {
                        $deadline = Carbon::createFromFormat('d-m-Y', $row['deadline'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        try {
                            $deadline = Carbon::parse($row['deadline'])->format('Y-m-d');
                        } catch (\Exception $e) {
                            $deadline = null;
                        }
                    }
                }

                $pegawaiId = Pegawai::where('nama', $row['pegawai'])
                    ->whereHas('teams', function ($q) {
                        $q->whereIn('teams.id', $this->teamIds);
                    })
                    ->value('id');

                $jenisId = JenisPekerjaan::where('nama_pekerjaan', $row['jenis_pekerjaan'])->value('id');

                if (!$pegawaiId || !$jenisId) {
                    return null;
                }

                return new Tugas([
                    'nama_tugas'         => $row['nama_tugas'] ?? '-',
                    'pegawai_id'         => $pegawaiId,
                    'jenis_pekerjaan_id' => $jenisId,
                    'target'             => $row['target'] ?? 0,
                    'satuan'             => $row['satuan'] ?? '-',
                    'asal'               => $row['asal_instruksi'] ?? '-',
                    'deadline'           => $deadline,
                ]);
            }
        }, $request->file('file'));

        return redirect()->route('admin.pekerjaan.index')
            ->with('success', 'Data tugas berhasil diimport.');
    }
}
