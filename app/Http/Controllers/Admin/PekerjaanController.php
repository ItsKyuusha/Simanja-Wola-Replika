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
        $teamId = auth()->user()->pegawai->team_id;
        $search = $request->input('search');

        $tugas = Tugas::with(['pegawai', 'jenisPekerjaan', 'realisasi'])
            ->whereHas('pegawai', function ($query) use ($teamId, $search) {
                $query->where('team_id', $teamId);

                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                            ->orWhere('nip', 'like', "%{$search}%");
                    });
                }
            })
            ->when($search, function ($query) use ($search) {
                $query->orWhere('nama_tugas', 'like', "%{$search}%");
            })
            ->get();

        $pegawai = Pegawai::where('team_id', $teamId)->get();
        $jenisPekerjaan = JenisPekerjaan::all();

        return view('admin.pekerjaan.index', compact('tugas', 'pegawai', 'jenisPekerjaan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tugas' => 'required',
            'pegawai_id' => 'required|exists:pegawais,id',
            'jenis_pekerjaan_id' => 'required|exists:jenis_pekerjaans,id',
            'target' => 'required|numeric',
            'satuan' => 'required|string',
            'deadline' => 'required|date',
        ]);

        $asalInstruksi = auth()->user()->pegawai->team->nama_tim ?? 'Tidak diketahui';

        Tugas::create([
            'nama_tugas'         => $request->nama_tugas,
            'pegawai_id'         => $request->pegawai_id,
            'jenis_pekerjaan_id' => $request->jenis_pekerjaan_id,
            'target'             => $request->target,
            'satuan'             => $request->satuan,
            'asal'               => $asalInstruksi,
            'deadline'           => $request->deadline,
        ]);

        return redirect()->route('admin.pekerjaan.index')->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tugas' => 'required',
            'pegawai_id' => 'required|exists:pegawais,id',
            'jenis_pekerjaan_id' => 'required|exists:jenis_pekerjaans,id',
            'target' => 'required|numeric',
            'satuan' => 'required|string',
            'deadline' => 'required|date',
        ]);

        $tugas = Tugas::findOrFail($id);
        $asalInstruksi = auth()->user()->pegawai->team->nama_tim ?? 'Tidak diketahui';

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
        return Excel::download(new class implements FromCollection, WithHeadings, WithStyles {
            public function collection()
            {
                return Tugas::with(['pegawai', 'jenisPekerjaan'])->get()->map(function ($tugas, $index) {
                    return [
                        'No'              => $index + 1,
                        'Nama Tugas'      => $tugas->nama_tugas,
                        'Pegawai'         => $tugas->pegawai->nama ?? '-',
                        'Jenis Pekerjaan' => $tugas->jenisPekerjaan->nama_pekerjaan ?? '-', // ✅ perbaikan
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

            // Tambahkan styling
            public function styles(Worksheet $sheet)
            {
                $highestRow    = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Header
                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ]
                    ]
                ]);

                // Data
                $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'left'],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ]
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

        Excel::import(new class implements \Maatwebsite\Excel\Concerns\ToModel, \Maatwebsite\Excel\Concerns\WithHeadingRow {
            public function model(array $row)
            {
                // Convert deadline (dd-mm-yyyy) ke format database (Y-m-d)
                $deadline = null;
                if (!empty($row['deadline'])) {
                    try {
                        $deadline = Carbon::createFromFormat('d-m-Y', $row['deadline'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        try {
                            // fallback parse format bebas (misalnya 2025-09-03)
                            $deadline = Carbon::parse($row['deadline'])->format('Y-m-d');
                        } catch (\Exception $e) {
                            $deadline = null;
                        }
                    }
                }

                // Cari pegawai & jenis pekerjaan
                $pegawaiId = Pegawai::where('nama', $row['pegawai'])->value('id');
                $jenisId   = JenisPekerjaan::where('nama_pekerjaan', $row['jenis_pekerjaan'])->value('id');


                // Kalau data penting tidak ada → skip baris
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
