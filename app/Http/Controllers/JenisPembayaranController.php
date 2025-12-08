<?php

namespace App\Http\Controllers;

use App\Models\JenisPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JenisPembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = JenisPembayaran::latest()->get();
        return view('admin.jenispembayaran.index', compact('data'));
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
            'nama_pembayaran' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_from', 'tambah_jenis_pembayaran');
        }

        JenisPembayaran::create($validator->validated());

        return redirect()->route('jenis-pembayaran.index')->with('success', 'Jenis pembayaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(JenisPembayaran $jenisPembayaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JenisPembayaran $jenisPembayaran)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisPembayaran $jenisPembayaran)
    {
        $validator = Validator::make($request->all(), [
            'nama_pembayaran' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_from', 'edit_jenis_pembayaran')
                ->with('edit_id', $jenisPembayaran->id);
        }

        $jenisPembayaran->update($validator->validated());

        return redirect()->route('jenis-pembayaran.index')->with('success', 'Jenis pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(JenisPembayaran $jenisPembayaran)
    {
        $jenisPembayaran->delete();

        return redirect()->route('jenis-pembayaran.index')->with('success', 'Jenis pembayaran berhasil dihapus.');
    }
}
