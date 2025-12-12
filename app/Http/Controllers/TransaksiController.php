<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use App\Models\Siswa;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     return view('admin.transaksi.index', [
    //         'transaksi' => Transaksi::with('siswa')->latest()->get(),
    //         'siswa'     => Siswa::orderBy('nama')->get(),
    //     ]);
    // }

    /**
     * Hanya transaksi MASUK
     */
    public function indexMasuk()
    {
        return view('admin.transaksi.masuk.index');
    }

    public function dataMasuk(Request $request)
    {
        $columns = [
            'id',
            'tanggal',
            'kode_transaksi',
            'jenis_pembayaran_id',
            'siswa_id',
            'nominal'
        ];

        $search = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        // Query awal
        $query = Transaksi::with(['siswa.kelas'])
            ->where('tipe', 'masuk');

        // Filtering
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%$search%")
                    ->orWhere('nominal', 'like', "%$search%")

                    // cari di siswa.nama
                    ->orWhereHas('siswa', function ($qs) use ($search) {
                        $qs->where('nama', 'like', "%$search%");
                    })

                    // cari di siswa.kelas.nama_kelas
                    ->orWhereHas('siswa.kelas', function ($qc) use ($search) {
                        $qc->where('nama_kelas', 'like', "%$search%");
                    });
            });
        }

        $recordsTotal = Transaksi::where('tipe', 'masuk')->count();
        $recordsFiltered = $query->count();

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $data = $query
            ->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        // Format data untuk DataTables
        $result = [];
        foreach ($data as $index => $item) {
            $jenis_ids = json_decode($item->jenis_pembayaran_id, true);
            $jenisList = \App\Models\JenisPembayaran::whereIn('id', $jenis_ids)->pluck('nama_pembayaran')->toArray();

            $result[] = [
                $start + $index + 1,
                $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i') : '-',
                $item->kode_transaksi,
                implode(', ', $jenisList),
                $item->siswa->nama ?? '-',
                $item->siswa->kelas->nama_kelas ?? '-',
                "Rp " . number_format($item->nominal, 0, ',', '.'),
                '<a href="' . route('transaksi.kwitansi', $item->id) . '" class="btn btn-info btn-sm" target="_blank">Cetak</a>',
                '
                <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                            data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>

                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="' . route('transaksi.masuk.edit', $item->id) . '">
                            <i class="bx bx-edit-alt me-1"></i> Edit
                        </a>

                        <form action="' . route('transaksi.destroy', $item->id) . '"
                              method="POST" class="d-inline"
                              onsubmit="return confirm(\'Yakin ingin hapus?\')">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button class="dropdown-item text-danger"><i class="bx bx-trash me-1"></i> Hapus</button>
                        </form>
                    </div>
                </div>
            '
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $result,
        ]);
    }

    /**
     * Hanya transaksi KELUAR
     */
    public function indexKeluar()
    {
        return view('admin.transaksi.keluar.index');
    }

    public function dataKeluar(Request $request)
    {
        $columns = [
            'id',
            'tanggal',
            'kode_transaksi',
            'nominal',
            'keterangan'
        ];

        $search = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $query = Transaksi::where('tipe', 'keluar');

        // SEARCH
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%$search%")
                    ->orWhere('nominal', 'like', "%$search%")
                    ->orWhere('keterangan', 'like', "%$search%");
            });
        }

        $recordsTotal = Transaksi::where('tipe', 'keluar')->count();
        $recordsFiltered = $query->count();

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $data = $query->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $result = [];

        foreach ($data as $index => $item) {

            // Button CETAK (tombol biru seperti gambar)
            $cetak = '
            <a href="' . route('transaksi.kwitansi', $item->id) . '"
                class="btn btn-info btn-sm"
                style="background:#04C8FF; border-color:#04C8FF;"
                target="_blank">
                Cetak
            </a>
        ';

            // Dropdown Aksi (edit + hapus)
            $aksi = '
        <div class="dropdown">
            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                data-bs-toggle="dropdown">
                <i class="bx bx-dots-vertical-rounded"></i>
            </button>

            <div class="dropdown-menu">

                <a href="#" 
                    class="dropdown-item btn-edit" 
                    data-id="' . $item->id . '">
                    <i class="bx bx-edit-alt me-1"></i> Edit
                </a>

                <form action="' . route('transaksi.destroy', $item->id) . '"
                    method="POST"
                    onsubmit="return confirm(\'Yakin ingin hapus?\')"
                    class="d-inline">

                    ' . csrf_field() . method_field('DELETE') . '

                    <button class="dropdown-item text-danger">
                        <i class="bx bx-trash me-1"></i> Hapus
                    </button>
                </form>

            </div>
        </div>';

            $result[] = [
                $start + $index + 1,
                \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i'),
                $item->kode_transaksi,
                "Rp " . number_format($item->nominal, 0, ',', '.'),
                $item->keterangan ?? '-',
                $cetak,        // kolom baru
                $aksi          // kolom aksi
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $result,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createMasuk()
    {
        return view('admin.transaksi.masuk.create', [
            'siswa' => Siswa::orderBy('nama')->get(),
            'jenis_pembayaran' => JenisPembayaran::orderBy('nama_pembayaran')->get(),
        ]);
    }

    // === GENERATE KODE TRANSAKSI ===
    private function generateKodeTransaksi()
    {
        $today = now()->format('Ymd');

        // Hitung transaksi hari ini
        $countToday = Transaksi::whereDate('tanggal', now()->toDateString())->count() + 1;

        // Format nomor urut 4 digit
        $urut = str_pad($countToday, 4, '0', STR_PAD_LEFT);

        return "TRX-$today-$urut";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ==============================
        // VALIDATION RULES DASAR
        // ==============================
        $rules = [
            'tanggal' => 'required|date',
            'tipe'    => 'required|in:masuk,keluar',
            'keterangan' => 'nullable|string',
        ];

        // ==============================
        // VALIDASI KHUSUS TIPE MASUK
        // ==============================
        if ($request->tipe === 'masuk') {

            $rules['siswa_id'] = 'required|exists:siswa,id';
            $rules['metode']   = 'required|in:tunai,transfer';

            // jenis pembayaran array
            $rules['jenis_pembayaran_id']   = 'required|array|min:1';
            $rules['jenis_pembayaran_id.*'] = 'exists:jenis_pembayarans,id';

            // nominal per jenis array
            $rules['nominal']   = 'required|array';
            $rules['nominal.*'] = 'required';
        }

        // ==============================
        // VALIDASI KHUSUS TIPE KELUAR
        // ==============================
        if ($request->tipe === 'keluar') {
            // nominal hanya angka biasa
            $rules['nominal'] = 'required';
        }

        // Jalankan validator
        $validator = Validator::make($request->all(), $rules);

        // ATTR NAMA UNTUK NOMINAL TIPE MASUK
        if ($request->tipe === 'masuk') {
            $attributeNames = [];
            foreach ($request->jenis_pembayaran_id ?? [] as $jpID) {
                $nama = \App\Models\JenisPembayaran::find($jpID)->nama_pembayaran ?? 'Nominal';
                $attributeNames["nominal.$jpID"] = "Nominal $nama";
            }
            $validator->setAttributeNames($attributeNames);
        }

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with('error_from', 'tambah_transaksi')
                ->withInput();
        }

        $data = $validator->validated();

        // ==============================
        // PROSES TIPE MASUK
        // ==============================
        if ($request->tipe === 'masuk') {

            // Bersihkan nominal (hilangkan titik)
            $cleanNominal = [];
            foreach ($request->nominal as $key => $value) {
                $cleanNominal[$key] = (int) str_replace('.', '', $value);
            }

            // Hitung total nominal
            $total_nominal = array_sum($cleanNominal);

            // Simpan data array
            $data['jenis_pembayaran_id'] = json_encode($request->jenis_pembayaran_id);
            $data['nominal_detail']      = json_encode($cleanNominal);

            // Kolom nominal utama = total
            $data['nominal'] = $total_nominal;
        }

        // ==============================
        // PROSES TIPE KELUAR
        // ==============================
        if ($request->tipe === 'keluar') {

            $data['siswa_id'] = null;
            // $data['metode']   = null;

            // nominal langsung angka (bukan total dari array)
            $data['nominal'] = (int) str_replace('.', '', $request->nominal);

            // kolom array set null
            $data['jenis_pembayaran_id'] = null;
            $data['nominal_detail'] = null;
        }

        // Tambahkan kode transaksi otomatis
        $data['kode_transaksi'] = $this->generateKodeTransaksi();

        // siapa yang buat
        $data['created_by'] = auth()->id();

        // SIMPAN
        Transaksi::create($data);

        $route = $request->tipe === 'masuk' ? 'transaksi.masuk' : 'transaksi.keluar';

        return redirect()->route($route)->with('success', 'Transaksi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        return response()->json($transaksi);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function editMasuk($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $jenis_pembayaran = JenisPembayaran::all();
        $siswa = Siswa::orderBy('nama')->get();

        // JSON decode detail nominal
        $detail = json_decode($transaksi->nominal_detail, true) ?? [];

        return view('admin.transaksi.masuk.edit', [
            'transaksi' => $transaksi,
            'jenis_pembayaran' => $jenis_pembayaran,
            'detail' => $detail,
            'siswa' => $siswa
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDATION RULES DASAR
    |--------------------------------------------------------------------------
    */
        $rules = [
            'tanggal'    => 'required|date',
            'tipe'       => 'required|in:masuk,keluar',
            'keterangan' => 'nullable|string',
        ];

        /*
    |--------------------------------------------------------------------------
    | VALIDASI KHUSUS TIPE MASUK
    |--------------------------------------------------------------------------
    */
        if ($request->tipe === 'masuk') {

            $rules['siswa_id'] = 'required|exists:siswa,id';
            $rules['metode']   = 'required|in:tunai,transfer';

            // Jenis pembayaran array
            $rules['jenis_pembayaran_id']   = 'required|array|min:1';
            $rules['jenis_pembayaran_id.*'] = 'exists:jenis_pembayarans,id';

            // Nominal array per jenis
            $rules['nominal']   = 'required|array';
            $rules['nominal.*'] = 'required';
        }

        /*
    |--------------------------------------------------------------------------
    | VALIDASI KHUSUS TIPE KELUAR
    |--------------------------------------------------------------------------
    */
        if ($request->tipe === 'keluar') {

            // Nominal hanya angka biasa, bukan array
            $rules['nominal'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        /*
    |--------------------------------------------------------------------------
    | ATTRIBUT NAMA UNTUK TIPE MASUK (NOMINAL PER JENIS)
    |--------------------------------------------------------------------------
    */
        if ($request->tipe === 'masuk') {
            $attributeNames = [];

            foreach ($request->jenis_pembayaran_id ?? [] as $jpID) {
                $nama = \App\Models\JenisPembayaran::find($jpID)->nama_pembayaran ?? 'Nominal';
                $attributeNames["nominal.$jpID"] = "Nominal $nama";
            }

            $validator->setAttributeNames($attributeNames);
        }

        if ($validator->fails()) {

            // Base response (TIDAK DIUBAH)
            $response = back()
                ->withErrors($validator)
                ->with('error_from', 'edit_transaksi')
                ->withInput();

            // Tambahkan edit_id HANYA ketika tipe = keluar
            if ($request->tipe === 'keluar') {
                $response->with('edit_id', $transaksi->id);
            }

            return $response;
        }

        $data = $validator->validated();

        /*
    |--------------------------------------------------------------------------
    | PROSES TIPE MASUK
    |--------------------------------------------------------------------------
    */
        if ($request->tipe === 'masuk') {

            // Bersihkan nominal array
            $cleanNominal = [];
            foreach ($request->nominal as $key => $value) {
                $cleanNominal[$key] = (int) str_replace('.', '', $value);
            }

            // Hitung total nominal
            $total_nominal = array_sum($cleanNominal);

            // Simpan JSON data
            $data['jenis_pembayaran_id'] = json_encode($request->jenis_pembayaran_id);
            $data['nominal_detail']      = json_encode($cleanNominal);

            // Nominal utama = total
            $data['nominal'] = $total_nominal;
        }

        /*
    |--------------------------------------------------------------------------
    | PROSES TIPE KELUAR
    |--------------------------------------------------------------------------
    */
        if ($request->tipe === 'keluar') {

            // Bersihkan nominal jadi integer
            $data['nominal'] = (int) str_replace('.', '', $request->nominal);

            // Set field tidak dipakai menjadi null
            $data['jenis_pembayaran_id'] = null;
            $data['nominal_detail']      = null;

            $data['siswa_id'] = null;
            // $data['metode']   = null;
        }

        /*
    |--------------------------------------------------------------------------
    | UPDATE DATA
    |--------------------------------------------------------------------------
    */
        $transaksi->update($data);

        $route = $request->tipe === 'masuk' ? 'transaksi.masuk' : 'transaksi.keluar';

        return redirect()->route($route)->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    public function history($siswa_id)
    {
        $data = Transaksi::where('siswa_id', $siswa_id)
            ->orderBy('tanggal', 'desc')
            ->take(20)
            ->get();

        $html = "";

        if ($data->count() == 0) {
            $html .= "<small class='text-muted'>Belum ada transaksi.</small>";
        } else {
            foreach ($data as $h) {

                // Ambil semua jenis pembayaran
                $jenis = json_decode($h->jenis_pembayaran_id, true) ?? [];
                $namaJenis = \App\Models\JenisPembayaran::whereIn('id', $jenis)
                    ->pluck('nama_pembayaran')
                    ->toArray();

                $html .= "
            <div class='history-item'>
                <div class='history-header'>
                    <div class='history-title'>" . implode(", ", $namaJenis) . "</div>
                    <div class='history-date'>" . \Carbon\Carbon::parse($h->tanggal)->format('d-m-Y H:i') . "</div>
                </div>

                <div class='history-nominal'>
                    Rp " . number_format($h->nominal, 0, ',', '.') . "
                </div>

                <div class='history-ket'>
                    " . ($h->keterangan ?: '<i>Tidak ada keterangan</i>') . "
                </div>
            </div>
            ";
            }
        }

        return response()->json(['html' => $html]);
    }

    public function kwitansi(Transaksi $transaksi)
    {
        // Hitung nomor urut berdasarkan tipe transaksi
        $noUrut = Transaksi::where('tipe', $transaksi->tipe)
            ->where('id', '<=', $transaksi->id)
            ->count();

        return view('admin.transaksi.kwitansi', [
            't' => $transaksi,
        ]);
    }

    public function print(Request $request)
    {
        $tahun = $request->tahun ?? date('Y');
        $from  = $request->from;
        $to    = $request->to;

        // ===============================
        // Query data utama
        // ===============================

        $query = Transaksi::whereYear('tanggal', $tahun);

        if ($from) {
            $query->whereDate('tanggal', '>=', $from);
        }
        if ($to) {
            $query->whereDate('tanggal', '<=', $to);
        }

        // ★ Tidak ada grouping — semua transaksi diambil apa adanya
        $rawData = $query->orderBy('tanggal', 'asc')->get();

        // ===============================
        // HITUNG SALDO AWAL
        // ===============================

        $saldoAwal = 0;

        if ($from) {
            $before = Transaksi::whereDate('tanggal', '<', $from)->get();

            foreach ($before as $b) {
                if ($b->tipe === 'masuk') {
                    $saldoAwal += $b->nominal;
                } else {
                    $saldoAwal -= $b->nominal;
                }
            }
        }

        // ===============================
        // BANGUN DATA PER BARIS (TANPA GROUPING)
        // ===============================

        $saldo = $saldoAwal;
        $rows = [];

        foreach ($rawData as $r) {

            // update saldo berjalan
            if ($r->tipe === 'masuk') {
                $saldo += $r->nominal;
            } else {
                $saldo -= $r->nominal;
            }

            $rows[] = [
                'tanggal'     => date('d-m-Y H:i', strtotime($r->tanggal)),
                'uraian'      => $r->keterangan,       // ★ langsung dari DB
                'no_bukti'    => $r->kode_transaksi,  // ★ langsung dari DB

                'penerimaan'  => $r->tipe === 'masuk'
                    ? number_format($r->nominal, 0, ',', '.')
                    : '',

                'pengeluaran' => $r->tipe === 'keluar'
                    ? number_format($r->nominal, 0, ',', '.')
                    : '',

                'saldo'       => number_format($saldo, 0, ',', '.'),
            ];
        }

        // ===============================
        // NAMA BULAN
        // ===============================

        if ($from) {
            $namaBulan = strtoupper(\Carbon\Carbon::parse($from)->translatedFormat('F'));
        } else {
            $namaBulan = '....................';
        }

        // ===============================
        // PERIODE FILTER
        // ===============================

        if ($from && $to) {
            $periode = \Carbon\Carbon::parse($from)->translatedFormat('d F Y')
                . ' s/d ' .
                \Carbon\Carbon::parse($to)->translatedFormat('d F Y');
        } elseif ($from && !$to) {
            $periode = \Carbon\Carbon::parse($from)->translatedFormat('d F Y') . ' s/d ...';
        } elseif (!$from && $to) {
            $periode = '... s/d ' . \Carbon\Carbon::parse($to)->translatedFormat('d F Y');
        } else {
            $periode = "Tahun $tahun";
        }

        // ===============================
        // TANGGAL TANDA TANGAN
        // ===============================

        $today = \Carbon\Carbon::now()->translatedFormat('d F Y');

        // ===============================
        // KIRIM KE BLADE
        // ===============================

        return view('admin.bukukas.print', compact('rows', 'namaBulan', 'tahun', 'today', 'periode'));
    }
}
