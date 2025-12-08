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
        return view('admin.transaksi.masuk.index', [
            'transaksi' => Transaksi::with('siswa')
                ->where('tipe', 'masuk')
                ->latest()
                ->get(),
            'siswa' => Siswa::orderBy('nama')->get(),
            'jenis_pembayaran' => JenisPembayaran::orderBy('nama_pembayaran')->get(),
        ]);
    }

    /**
     * Hanya transaksi KELUAR
     */
    public function indexKeluar()
    {
        return view('admin.transaksi.keluar.index', [
            'transaksi' => Transaksi::with('siswa')
                ->where('tipe', 'keluar')
                ->latest()
                ->get(),
            // 'siswa' => Siswa::orderBy('nama')->get(),
        ]);
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
        // Hapus titik pada nominal
        $cleanNominal = str_replace('.', '', $request->nominal);
        $request->merge(['nominal' => $cleanNominal]);

        // RULE DASAR
        $rules = [
            'tanggal'   => 'required|date',
            'tipe'      => 'required|in:masuk,keluar',
            'deskripsi' => 'required|string|max:255',
            'nominal'   => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',

            // WAJIB UNTUK SEMUA TRANSAKSI
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayarans,id',
        ];

        // VALIDASI TAMBAHAN KHUSUS PEMASUKAN
        if ($request->tipe === 'masuk') {
            $rules['siswa_id'] = 'nullable|exists:siswa,id';
            $rules['metode']   = 'nullable|in:tunai,transfer';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with('error_from', 'tambah_transaksi');
        }

        $data = $validator->validated();

        // Jika pengeluaran → kosongkan siswa dan metode
        if ($request->tipe === 'keluar') {
            $data['siswa_id'] = null;
            $data['metode'] = null;
        }

        $data['created_by'] = auth()->id();

        Transaksi::create($data);

        return back()->with('success', 'Transaksi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaksi $transaksi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaksi $transaksi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        // Hapus titik pada nominal
        $cleanNominal = str_replace('.', '', $request->nominal);
        $request->merge(['nominal' => $cleanNominal]);

        // RULE DASAR
        $rules = [
            'tanggal'   => 'required|date',
            'tipe'      => 'required|in:masuk,keluar',
            'deskripsi' => 'required|string|max:255',
            'nominal'   => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',

            // WAJIB UNTUK SEMUA TRANSAKSI
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayarans,id',
        ];

        // VALIDASI KHUSUS PEMASUKAN
        if ($request->tipe === 'masuk') {
            $rules['siswa_id'] = 'nullable|exists:siswa,id';
            $rules['metode']   = 'nullable|in:tunai,transfer';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with('error_from', 'edit_transaksi')
                ->with('edit_id', $transaksi->id);
        }

        $data = $validator->validated();

        // Jika tipe = keluar → siswa & metode harus null
        if ($request->tipe === 'keluar') {
            $data['siswa_id'] = null;
            $data['metode'] = null;
        }

        $transaksi->update($data);

        return back()->with('success', 'Transaksi berhasil diperbarui.');
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
                $html .= "
            <div class='d-flex justify-content-between border-bottom py-1'>
                <div>
                    <strong>" . ucfirst($h->tipe) . "</strong><br>
                    " . $h->deskripsi . "<br>
                    <small class='text-muted'>" . $h->tanggal . "</small>
                </div>
                <div class='text-end'>
                    Rp " . number_format($h->nominal, 0, ',', '.') . "
                </div>
            </div>";
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
            'no_urut' => $noUrut
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
        // GROUPING PER TANGGAL
        // ===============================

        $grouped = [];

        foreach ($rawData as $r) {

            $key = $r->tanggal;

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'tanggal'     => date('d-m-Y', strtotime($r->tanggal)),
                    'penerimaan'  => 0,
                    'pengeluaran' => 0,
                ];
            }

            if ($r->tipe === 'masuk') {
                $grouped[$key]['penerimaan'] += $r->nominal;
            } else {
                $grouped[$key]['pengeluaran'] += $r->nominal;
            }
        }

        // ===============================
        // HITUNG SALDO BERJALAN
        // ===============================

        $saldo = $saldoAwal;
        $rows  = [];

        foreach ($grouped as $tgl => $g) {

            $saldo += ($g['penerimaan'] - $g['pengeluaran']);

            $rows[] = [
                'tanggal'     => $g['tanggal'],

                // Uraian manual (sesuai permintaan)
                'uraian'      => 'Transaksi Tanggal Ini',

                // Nomor bukti otomatis: BK-YYYYMMDD
                'no_bukti'    => 'BK-' . str_replace('-', '', $tgl),

                'penerimaan'  => $g['penerimaan'] > 0
                    ? number_format($g['penerimaan'], 0, ',', '.')
                    : '',
                'pengeluaran' => $g['pengeluaran'] > 0
                    ? number_format($g['pengeluaran'], 0, ',', '.')
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
