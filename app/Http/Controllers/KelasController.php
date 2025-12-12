<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.kelas.index');
    }

    public function data(Request $request)
    {
        $columns = ['id', 'nama_kelas', 'angkatan'];

        $search = $request->input('search.value');
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $query = Kelas::query();

        // SEARCH
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_kelas', 'like', "%$search%")
                    ->orWhere('angkatan', 'like', "%$search%");
            });
        }

        $recordsTotal = Kelas::count();
        $recordsFiltered = $query->count();

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $data = $query->orderBy($orderColumn, $orderDir)
            ->skip($start)
            ->take($length)
            ->get();

        $result = [];

        foreach ($data as $index => $item) {

            // Dropdown aksi
            $aksi = '
        <div class="dropdown">
            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                data-bs-toggle="dropdown">
                <i class="bx bx-dots-vertical-rounded"></i>
            </button>

            <div class="dropdown-menu">

                <a href="#" class="dropdown-item btn-edit" data-id="' . $item->id . '">
                    <i class="bx bx-edit-alt me-1"></i> Edit
                </a>

                <form method="POST" action="' . route('kelas.destroy', $item->id) . '"
                      onsubmit="return confirm(\'Yakin ingin hapus?\')">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button class="dropdown-item text-danger">
                        <i class="bx bx-trash me-1"></i> Hapus
                    </button>
                </form>

            </div>
        </div>';

            $result[] = [
                $start + $index + 1,         // nomor
                $item->nama_kelas,
                $item->angkatan,
                $aksi
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
            'nama_kelas' => 'required|string|max:255',
            'angkatan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_from', 'tambah_kelas');
        }

        Kelas::create($validator->validated());

        return back()->with('success', 'Kelas berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas)
    {
        return response()->json($kelas);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kelas)
    {
        $validator = Validator::make($request->all(), [
            'nama_kelas' => 'required|string|max:255',
            'angkatan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_from', 'edit_kelas')
                ->with('edit_id', $kelas->id);
        }

        $kelas->update($validator->validated());

        return back()->with('success', 'Kelas berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return back()->with('success', 'Kelas berhasil dihapus');
    }
}
