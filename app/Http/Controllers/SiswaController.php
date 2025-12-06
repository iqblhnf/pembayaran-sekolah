<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
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
}
