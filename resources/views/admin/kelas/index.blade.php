@extends('layouts.app')

@section('title', 'Kelas')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Kelas</h4>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    <!-- Responsive Table -->
    <div class="card p-2">
        <h5 class="card-header">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKelas">
                Tambah Kelas
            </button>
        </h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered" id="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>#</th>
                        <th>Nama Kelas</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kelas as $item)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $item->nama_kelas }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editKelas{{ $item->id }}">
                                Edit
                            </button>
                            <form action="{{ route('kelas.destroy', $item->id) }}" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin hapus kelas?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <div class="modal fade" id="editKelas{{ $item->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h1 class="modal-title fs-5">Edit Kelas</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <form action="{{ route('kelas.update', $item->id) }}" method="post">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-body">

                                        {{-- NAMA KELAS --}}
                                        <div class="mb-3">
                                            <label class="col-form-label">Nama Kelas</label>
                                            <input type="text"
                                                class="form-control"
                                                name="nama_kelas"
                                                value="{{ old('nama_kelas', $item->nama_kelas) }}">

                                            {{-- Error hanya muncul untuk modal edit kelas yang ID-nya cocok --}}
                                            @if (session('error_from') === 'edit_kelas' && session('edit_id') == $item->id)
                                            @error('nama_kelas')
                                            <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            @endif
                                        </div>

                                        {{-- KETERANGAN --}}
                                        <div class="mb-3">
                                            <label class="col-form-label">Keterangan</label>
                                            <textarea class="form-control" name="keterangan" rows="5">{{ old('keterangan', $item->keterangan) }}</textarea>

                                            @if (session('error_from') === 'edit_kelas' && session('edit_id') == $item->id)
                                            @error('keterangan')
                                            <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                            @endif
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!--/ Responsive Table -->
</div>

<!-- Modal -->
<div class="modal fade" id="tambahKelas" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5">Tambah Kelas</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('kelas.store') }}" method="post">
                @csrf

                <div class="modal-body">

                    {{-- NAMA KELAS --}}
                    <div class="mb-3">
                        <label class="col-form-label">Nama Kelas</label>
                        <input type="text" class="form-control" name="nama_kelas" value="{{ old('nama_kelas') }}">

                        @if (session('error_from') === 'tambah_kelas')
                        @error('nama_kelas')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="mb-3">
                        <label class="col-form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="5">{{ old('keterangan') }}</textarea>

                        @if (session('error_from') === 'tambah_kelas')
                        @error('keterangan')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>

@if ($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {

        @if(session('error_from') === 'edit_kelas')
        var modalEdit = new bootstrap.Modal(
            document.getElementById("editKelas{{ session('edit_id') }}")
        );
        modalEdit.show();

        @elseif(session('error_from') === 'tambah_kelas')
        var modalAdd = new bootstrap.Modal(
            document.getElementById("tambahKelas")
        );
        modalAdd.show();
        @endif

    });
</script>
@endif

@endsection