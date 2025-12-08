@extends('layouts.app')

@section('title', 'Jenis Pembayaran')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Jenis Pembayaran</h4>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    <!-- Responsive Table -->
    <div class="card p-2">
        <h5 class="card-header">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahJenisPembayaran">
                Tambah Jenis Pembayaran
            </button>
        </h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-bordered" id="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>#</th>
                        <th>Nama Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $item->nama_pembayaran }}</td>
                        <td>
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editJenisPembayaran{{ $item->id }}">
                                Edit
                            </button>
                            <form action="{{ route('jenis-pembayaran.destroy', $item->id) }}" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin hapus kelas?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <div class="modal fade" id="editJenisPembayaran{{ $item->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h1 class="modal-title fs-5">Edit Jenis Pembayaran</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <form action="{{ route('jenis-pembayaran.update', $item->id) }}" method="post">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-body">

                                        {{-- NAMA PEMBAYARAN --}}
                                        <div class="mb-3">
                                            <label class="col-form-label">Nama Pembayaran</label>
                                            <input type="text"
                                                class="form-control"
                                                name="nama_pembayaran"
                                                value="{{ old('nama_pembayaran', $item->nama_pembayaran) }}">

                                            {{-- Error hanya muncul untuk modal edit jenis pembayaran yang ID-nya cocok --}}
                                            @if (session('error_from') === 'edit_jenis_pembayaran' && session('edit_id') == $item->id)
                                            @error('nama_pembayaran')
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
<div class="modal fade" id="tambahJenisPembayaran" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5">Tambah Jenis Pembayaran</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('jenis-pembayaran.store') }}" method="post">
                @csrf

                <div class="modal-body">

                    {{-- NAMA PEMBAYARAN --}}
                    <div class="mb-3">
                        <label class="col-form-label">Nama Pembayaran</label>
                        <input type="text" class="form-control" name="nama_pembayaran" value="{{ old('nama_pembayaran') }}">

                        @if (session('error_from') === 'tambah_jenis_pembayaran')
                        @error('nama_pembayaran')
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

        @if(session('error_from') === 'edit_jenis_pembayaran')
        var modalEdit = new bootstrap.Modal(
            document.getElementById("editJenisPembayaran{{ session('edit_id') }}")
        );
        modalEdit.show();

        @elseif(session('error_from') === 'tambah_jenis_pembayaran')
        var modalAdd = new bootstrap.Modal(
            document.getElementById("tambahJenisPembayaran")
        );
        modalAdd.show();
        @endif

    });
</script>
@endif

@endsection