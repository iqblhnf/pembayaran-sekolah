<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'postLogin'])->name('postLogin');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('kelas', KelasController::class)->parameters([
        'kelas' => 'kelas'
    ]);
    Route::resource('siswa', SiswaController::class);

    Route::get('/transaksi/masuk', [TransaksiController::class, 'indexMasuk'])->name('transaksi.masuk');
    Route::get('/transaksi/keluar', [TransaksiController::class, 'indexKeluar'])->name('transaksi.keluar');
    Route::resource('transaksi', TransaksiController::class);
    Route::get('/transaksi/history/{siswa}', [TransaksiController::class, 'history'])->name('transaksi.history');
    Route::get('/transaksi/{transaksi}/kwitansi', [TransaksiController::class, 'kwitansi'])->name('transaksi.kwitansi');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
