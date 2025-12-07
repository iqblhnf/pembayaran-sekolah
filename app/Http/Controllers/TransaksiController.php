<?php

namespace App\Http\Controllers;

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
            'siswa' => Siswa::orderBy('nama')->get(),
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

        $validator = Validator::make($request->all(), [
            'tanggal'   => 'required|date',
            'siswa_id'  => 'nullable|exists:siswa,id',
            'tipe'      => 'required|in:masuk,keluar',
            'deskripsi' => 'required|string|max:255',
            'nominal'   => 'required|numeric|min:1',
            'metode'    => 'nullable|in:tunai,transfer',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with('error_from', 'tambah_transaksi');
        }

        Transaksi::create($validator->validated() + [
            'created_by' => auth()->id(), // otomatis set user input
        ]);

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
        // Hapus titik pada nominal (supaya numeric)
        $cleanNominal = str_replace('.', '', $request->nominal);
        $request->merge(['nominal' => $cleanNominal]);

        $validator = Validator::make($request->all(), [
            'tanggal'   => 'required|date',
            'siswa_id'  => 'nullable|exists:siswa,id',
            'tipe'      => 'required|in:masuk,keluar',
            'deskripsi' => 'required|string|max:255',
            'nominal'   => 'required|numeric|min:1',
            'metode'    => 'nullable|in:tunai,transfer',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->with('error_from', 'edit_transaksi')
                ->with('edit_id', $transaksi->id);
        }

        $transaksi->update($validator->validated());

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
        return view('admin.transaksi.kwitansi', [
            't' => $transaksi
        ]);
    }
}
