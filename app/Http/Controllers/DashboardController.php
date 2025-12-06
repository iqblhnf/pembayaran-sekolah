<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $filterTipe = $request->input('tipe', 'gabungan');
        // nilai: gabungan | masuk | keluar

        /* ========================================================
         * TOTAL PEMASUKAN / PENGELUARAN
         * ====================================================== */
        $totalMasuk  = Transaksi::where('tipe', 'masuk')->whereYear('tanggal', $tahun)->sum('nominal');
        $totalKeluar = Transaksi::where('tipe', 'keluar')->whereYear('tanggal', $tahun)->sum('nominal');
        $saldoAkhir  = $totalMasuk - $totalKeluar;

        /*
        |--------------------------------------------------------------------------
        | MINGGUAN (Menyesuaikan filter: gabungan/masuk/keluar)
        |--------------------------------------------------------------------------
        */
        $weeklyQuery = Transaksi::select(
            DB::raw("strftime('%w', tanggal) AS hari"),
            DB::raw("SUM(nominal) AS total")
        )->whereYear('tanggal', $tahun);

        if ($filterTipe !== 'gabungan') {
            $weeklyQuery->where('tipe', $filterTipe);
        }

        $weeklyRaw = $weeklyQuery
            ->groupBy(DB::raw("strftime('%w', tanggal)"))
            ->get();

        $weeklyLabels = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $weeklyData = array_fill(0, 7, 0);

        foreach ($weeklyRaw as $row) {
            $weeklyData[(int)$row->hari] = $row->total;
        }

        /*
        |--------------------------------------------------------------------------
        | PER KELAS
        |--------------------------------------------------------------------------
        */
        $kelasQuery = DB::table('transaksi')
            ->join('siswa', 'siswa.id', '=', 'transaksi.siswa_id')
            ->join('kelas', 'kelas.id', '=', 'siswa.kelas_id')
            ->select('kelas.nama_kelas', DB::raw("SUM(transaksi.nominal) AS total"))
            ->whereYear('transaksi.tanggal', $tahun);

        if ($filterTipe !== 'gabungan') {
            $kelasQuery->where('transaksi.tipe', $filterTipe);
        }

        $kelasRaw = $kelasQuery
            ->groupBy('kelas.id')
            ->orderBy('total', 'DESC')
            ->get();

        $kelasLabels = $kelasRaw->pluck('nama_kelas')->toArray();
        $kelasTotals = $kelasRaw->pluck('total')->toArray();

        /*
        |--------------------------------------------------------------------------
        | JENIS TRANSAKSI (kata pertama deskripsi)
        |--------------------------------------------------------------------------
        */
        $jenisQuery = Transaksi::select(
            DB::raw("
                LOWER(
                    CASE
                        WHEN instr(deskripsi, ' ') > 0
                            THEN substr(deskripsi, 1, instr(deskripsi,' ') - 1)
                        ELSE deskripsi
                    END
                ) AS kategori
            "),
            DB::raw("SUM(nominal) AS total")
        )
            ->whereYear('tanggal', $tahun);

        if ($filterTipe !== 'gabungan') {
            $jenisQuery->where('tipe', $filterTipe);
        }

        $jenisRaw = $jenisQuery->groupBy('kategori')->get();
        $jenisLabels = $jenisRaw->pluck('kategori')->toArray();
        $jenisTotals = $jenisRaw->pluck('total')->toArray();

        /*
        |--------------------------------------------------------------------------
        | METODE PEMBAYARAN
        |--------------------------------------------------------------------------
        */
        $metodeQuery = Transaksi::select('metode', DB::raw("SUM(nominal) AS total"))
            ->whereYear('tanggal', $tahun);

        if ($filterTipe !== 'gabungan') {
            $metodeQuery->where('tipe', $filterTipe);
        }

        $metodeRaw = $metodeQuery->groupBy('metode')->get();
        $metodeLabels = $metodeRaw->pluck('metode')->toArray();
        $metodeTotals = $metodeRaw->pluck('total')->toArray();

        /*
        |--------------------------------------------------------------------------
        | PER SISWA (TOP 10)
        |--------------------------------------------------------------------------
        */
        $siswaQuery = DB::table('transaksi')
            ->join('siswa', 'siswa.id', '=', 'transaksi.siswa_id')
            ->select('siswa.nama', DB::raw("SUM(transaksi.nominal) AS total"))
            ->whereYear('transaksi.tanggal', $tahun);

        if ($filterTipe !== 'gabungan') {
            $siswaQuery->where('transaksi.tipe', $filterTipe);
        }

        $siswaRaw = $siswaQuery
            ->groupBy('siswa.id')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get();

        $siswaLabels = $siswaRaw->pluck('nama')->toArray();
        $siswaTotals = $siswaRaw->pluck('total')->toArray();

        /*
        |--------------------------------------------------------------------------
        | BULANAN MASUK & KELUAR
        |--------------------------------------------------------------------------
        */
        // Data masuk
        $monthlyMasukRaw = Transaksi::select(
            DB::raw("strftime('%m', tanggal) AS bulan"),
            DB::raw("SUM(nominal) AS total")
        )
            ->where('tipe', 'masuk')
            ->whereYear('tanggal', $tahun)
            ->groupBy(DB::raw("strftime('%m', tanggal)"))
            ->get();

        // Data keluar
        $monthlyKeluarRaw = Transaksi::select(
            DB::raw("strftime('%m', tanggal) AS bulan"),
            DB::raw("SUM(nominal) AS total")
        )
            ->where('tipe', 'keluar')
            ->whereYear('tanggal', $tahun)
            ->groupBy(DB::raw("strftime('%m', tanggal)"))
            ->get();

        // Label bulan
        $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $monthMasukData  = array_fill(0, 12, 0);
        $monthKeluarData = array_fill(0, 12, 0);

        foreach ($monthlyMasukRaw as $m) {
            $monthMasukData[(int)$m->bulan - 1] = $m->total;
        }
        foreach ($monthlyKeluarRaw as $m) {
            $monthKeluarData[(int)$m->bulan - 1] = $m->total;
        }

        /*
        |--------------------------------------------------------------------------
        | Jika filter = MASUK saja → hanya ambil data masuk
        | Jika filter = KELUAR saja → hanya ambil data keluar
        | Jika gabungan → dua-duanya
        |--------------------------------------------------------------------------
        */
        if ($filterTipe === 'masuk') {
            $monthData = $monthMasukData;
        } elseif ($filterTipe === 'keluar') {
            $monthData = $monthKeluarData;
        } else {
            // gabungan → tetap kirim 2 series ke blade
            $monthData = null;
        }

        /* ========================================================
         * RETURN VIEW
         * ====================================================== */
        return view('admin.dashboard', compact(
            'tahun',
            'filterTipe',
            'totalMasuk',
            'totalKeluar',
            'saldoAkhir',
            'weeklyLabels',
            'weeklyData',
            'kelasLabels',
            'kelasTotals',
            'jenisLabels',
            'jenisTotals',
            'metodeLabels',
            'metodeTotals',
            'siswaLabels',
            'siswaTotals',
            'monthLabels',
            'monthData',
            'monthMasukData',
            'monthKeluarData'
        ));
    }
}
