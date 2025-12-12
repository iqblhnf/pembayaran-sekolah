<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'postLogin'])->name('postLogin');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/kelas/data', [KelasController::class, 'data'])->name('kelas.data');
    Route::get('/kelas/{id}/edit-modal', [KelasController::class, 'editModal'])->name('kelas.editModal');
    Route::resource('kelas', KelasController::class)->parameters([
        'kelas' => 'kelas'
    ]);

    Route::post('/siswa/import-preview', [SiswaController::class, 'importPreview'])->name('siswa.import.preview');
    Route::post('/siswa/import-confirm', [SiswaController::class, 'importConfirm'])->name('siswa.import.confirm');
    Route::get('/siswa/download-template', [SiswaController::class, 'downloadTemplate'])->name('siswa.download.template');
    Route::get('/siswa/{id}/riwayat', [SiswaController::class, 'riwayat'])->name('siswa.riwayat');
    Route::get('/siswa/data', [SiswaController::class, 'data'])->name('siswa.data');

    Route::resource('siswa', SiswaController::class);

    Route::resource('jenis-pembayaran', JenisPembayaranController::class);

    Route::get('/transaksi/masuk', [TransaksiController::class, 'indexMasuk'])->name('transaksi.masuk');
    Route::get('/transaksi/masuk/data', [TransaksiController::class, 'dataMasuk'])->name('transaksi.masuk.data');

    Route::get('/transaksi/masuk/create', [TransaksiController::class, 'createMasuk'])->name('transaksi.masuk.create');
    Route::get('/transaksi/masuk/edit/{id}', [TransaksiController::class, 'editMasuk'])->name('transaksi.masuk.edit');

    Route::get('/transaksi/keluar', [TransaksiController::class, 'indexKeluar'])->name('transaksi.keluar');
    Route::get('/transaksi/keluar/data', [TransaksiController::class, 'dataKeluar'])->name('transaksi.keluar.data');
    Route::get('/transaksi/keluar/{id}/show', [TransaksiController::class, 'show'])->name('transaksi.keluar.show');

    Route::resource('transaksi', TransaksiController::class);
    Route::get('/transaksi/history/{siswa}', [TransaksiController::class, 'history'])->name('transaksi.history');
    Route::get('/transaksi/{transaksi}/kwitansi', [TransaksiController::class, 'kwitansi'])->name('transaksi.kwitansi');

    Route::get('/buku-kas/print', [TransaksiController::class, 'print'])->name('bukuKas.print');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/backup/sqlite', function () {

        $path = database_path('database.sqlite');

        if (!file_exists($path)) {
            abort(404, 'File database tidak ditemukan.');
        }

        // Nama file backup (pakai timestamp agar unik)
        $fileName = 'backup_sqlite_' . date('Y-m-d_H-i-s') . '.sqlite';

        return response()->download($path, $fileName);
    })->name('backup.sqlite');
});
