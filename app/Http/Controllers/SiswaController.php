<?php

namespace App\Http\Controllers;

use App\Exports\TemplateSiswaExport;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $siswa = Siswa::with('kelas')->get();
        $kelas = Kelas::all();
        return view('admin.siswa.index', compact('siswa', 'kelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id'   => 'required|exists:kelas,id',
            'nis'        => 'required|unique:siswa,nis',
            'nama'       => 'required|string|max:255',
            'alamat'     => 'nullable|string',
            'nama_ortu'  => 'nullable|string|max:255',
            'telp_ortu'  => 'nullable|string|max:20',
            'status'     => 'required|in:aktif,lulus,keluar',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with('error_from', 'tambah_siswa');
        }

        Siswa::create($validator->validated());

        return back()->with('success', 'Siswa berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Siswa $siswa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Siswa $siswa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id'   => 'required|exists:kelas,id',
            'nis'        => 'required|unique:siswa,nis,' . $siswa->id,
            'nama'       => 'required|string|max:255',
            'alamat'     => 'nullable|string',
            'nama_ortu'  => 'nullable|string|max:255',
            'telp_ortu'  => 'nullable|string|max:20',
            'status'     => 'required|in:aktif,lulus,keluar',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with('error_from', 'edit_siswa')
                ->with('edit_id', $siswa->id);
        }

        $siswa->update($validator->validated());

        return back()->with('success', 'Data siswa berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return back()->with('success', 'Siswa berhasil dihapus');
    }

    public function riwayat($id)
    {
        $siswa = Siswa::with('kelas')->findOrFail($id);

        // Hanya ambil transaksi MASUK
        $riwayat = Transaksi::with('jenisPembayaran')
            ->where('siswa_id', $id)
            ->where('tipe', 'masuk')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('admin.siswa.riwayat', compact('siswa', 'riwayat'));
    }

    private function normalizeKelas($nama)
    {
        if (!$nama) return null;

        // Hilangkan spasi berlebih
        $nama = preg_replace('/\s+/', ' ', trim($nama));

        // Lowercase semua dulu agar konsisten
        $nama = strtolower($nama);

        // Pisah kata
        $parts = explode(' ', $nama);

        // Pemetaan romawi
        $roman = [
            'vii' => 'VII',
            'viii' => 'VIII',
            'ix' => 'IX',
            'x' => 'X',
            'xi' => 'XI',
            'xii' => 'XII',
        ];

        $result = [];
        $foundLevel = false;

        foreach ($parts as $p) {

            // 1) Jika kata adalah level romawi
            if (!$foundLevel && isset($roman[$p])) {
                $result[] = $roman[$p];
                $foundLevel = true;
                continue;
            }

            // 2) Jika angka kelas
            if (is_numeric($p)) {
                $result[] = $p; // angka tetap angka
                continue;
            }

            // 3) Selainnya → semua UPPERCASE
            $result[] = strtoupper($p);
        }

        return implode(' ', $result);
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        $path = $request->file('file')->store('temp', 'private');
        $fullPath = Storage::disk('private')->path($path);
        $data = Excel::toArray([], $fullPath)[0];

        if (count($data) < 2) {
            return back()->with('error', 'File Excel kosong.');
        }

        // Header
        $header = array_map('strtolower', $data[0]);
        $rows   = array_slice($data, 1);

        // Kolom wajib
        $required = ['nama', 'nis', 'kelas', 'angkatan'];
        foreach ($required as $col) {
            if (!in_array($col, $header)) {
                return back()->with('error', "Kolom '{$col}' tidak ditemukan.");
            }
        }

        $preview = [];
        $errors = [];
        $nisList = [];

        foreach ($rows as $i => $row) {

            $row = array_combine($header, $row);
            if (!$row) continue;

            // Normalisasi kelas sebelum preview
            $row['kelas'] = $this->normalizeKelas($row['kelas']);

            // Validasi NIS
            if (empty($row['nis'])) {
                $errors[] = "Baris " . ($i + 2) . ": NIS kosong.";
                continue;
            }

            // Duplikasi di dalam file
            if (in_array($row['nis'], $nisList)) {
                $errors[] = "Baris " . ($i + 2) . ": NIS duplikat dalam file.";
                continue;
            }
            $nisList[] = $row['nis'];

            // Cek apakah siswa sudah ada
            $existing = Siswa::where('nis', $row['nis'])->first();

            $preview[] = [
                'row'  => $row,
                'mode' => $existing ? 'update' : 'insert'
            ];
        }

        return view('admin.siswa.index', [
            'siswa' => Siswa::with('kelas')->get(),
            'kelas' => Kelas::all(),
            'preview' => $preview,
            'errors_preview' => $errors,
            'file_path' => $path
        ]);
    }

    public function importConfirm(Request $request)
    {
        $path = $request->file_path; // contoh: temp/abcd.xlsx

        if (!$path) {
            return back()->with('error', 'File path tidak ditemukan.');
        }

        // Path private/temp
        $fullPath = Storage::disk('private')->path($path);

        // Cek apakah file ada di PRIVATE
        if (!file_exists($fullPath)) {
            return back()->with('error', "File tidak ditemukan: $fullPath");
        }

        // Baca Excel dari PRIVATE
        $data = Excel::toArray([], $fullPath)[0];

        $header = array_map('strtolower', $data[0]);
        $rows = array_slice($data, 1);

        foreach ($rows as $row) {

            $row = array_combine($header, $row);
            if (!$row || empty($row['nis'])) continue;

            // Normalisasi nama kelas
            $normalizedKelas = $this->normalizeKelas($row['kelas']);

            // Cari kelas lama (case insensitive)
            $kelas = Kelas::whereRaw('LOWER(nama_kelas) = ?', [
                strtolower($normalizedKelas)
            ])->first();

            // Jika tidak ada → buat kelas baru
            if (!$kelas) {
                $kelas = Kelas::create([
                    'nama_kelas' => $normalizedKelas,
                    'angkatan'   => $row['angkatan']
                ]);
            }

            // Cari siswa
            $siswa = Siswa::where('nis', $row['nis'])->first();

            if ($siswa) {
                // UPDATE
                $siswa->update([
                    'nama'      => $row['nama'],
                    'kelas_id'  => $kelas->id,
                    'alamat'    => $row['alamat'] ?? null,
                    'nama_ortu' => $row['nama_ortu'] ?? null,
                    'telp_ortu' => $row['telp_ortu'] ?? null,
                    'status'    => $row['status'] ?? 'aktif',
                ]);
            } else {
                // INSERT
                Siswa::create([
                    'nama'      => $row['nama'],
                    'nis'       => $row['nis'],
                    'kelas_id'  => $kelas->id,
                    'alamat'    => $row['alamat'] ?? null,
                    'nama_ortu' => $row['nama_ortu'] ?? null,
                    'telp_ortu' => $row['telp_ortu'] ?? null,
                    'status'    => $row['status'] ?? 'aktif',
                ]);
            }
        }

        // Hapus file dari PRIVATE/temp
        Storage::disk('private')->delete($path);

        return redirect()->route('siswa.index')->with('success', 'Import selesai!');
    }

    public function downloadTemplate()
    {
        return Excel::download(new TemplateSiswaExport(), 'template_siswa.xlsx');
    }
}
