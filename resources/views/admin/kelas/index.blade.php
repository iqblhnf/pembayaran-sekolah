@extends('layouts.app')

@section('title', 'Kelas')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Kelas</h4>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="card p-2">
        <h5 class="card-header">
            <button type="button" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#tambahKelas">
                Tambah Kelas
            </button>
        </h5>

        <div class="table-responsive text-nowrap">
            <table class="table table-bordered" id="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>#</th>
                        <th>Nama Kelas</th>
                        <th>Angkatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================= -->
<!-- MODAL TAMBAH -->
<!-- ========================= -->
<div class="modal fade" id="tambahKelas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('kelas.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- NAMA KELAS --}}
                    <div class="mb-3">
                        <label>Nama Kelas</label>
                        <input type="text" class="form-control"
                            name="nama_kelas"
                            value="{{ session('error_from') === 'tambah_kelas' ? old('nama_kelas') : '' }}">

                        @if(session('error_from') === 'tambah_kelas')
                        @error('nama_kelas') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- ANGKATAN --}}
                    <div class="mb-3">
                        <label>Angkatan</label>
                        <input type="text" class="form-control"
                            name="angkatan"
                            value="{{ session('error_from') === 'tambah_kelas' ? old('angkatan') : '' }}">

                        @if(session('error_from') === 'tambah_kelas')
                        @error('angkatan') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ========================= -->
<!-- MODAL EDIT -->
<!-- ========================= -->
<div class="modal fade" id="modalEditKelas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formEditKelas" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- NAMA KELAS --}}
                    <div class="mb-3">
                        <label>Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control">

                        @if(session('error_from') === 'edit_kelas')
                        @error('nama_kelas') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- ANGKATAN --}}
                    <div class="mb-3">
                        <label>Angkatan</label>
                        <input type="text" name="angkatan" class="form-control">

                        @if(session('error_from') === 'edit_kelas')
                        @error('angkatan') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    $(function() {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('kelas.data') }}",
            columns: [{
                    data: 0
                },
                {
                    data: 1
                },
                {
                    data: 2
                },
                {
                    data: 3,
                    orderable: false,
                    searchable: false
                },
            ]
        });
    });


    // =========================
    // TOMBOL EDIT → AMBIL DATA
    // =========================
    $(document).on("click", ".btn-edit", function() {

        let id = $(this).data("id");

        $.get("/kelas/" + id, function(res) {

            $("#formEditKelas").attr("action", "/kelas/" + id);

            $("#modalEditKelas input[name='nama_kelas']").val(res.nama_kelas);
            $("#modalEditKelas input[name='angkatan']").val(res.angkatan);

            new bootstrap.Modal(document.getElementById("modalEditKelas")).show();
        });
    });
</script>


{{-- ========================= --}}
{{-- AUTO OPEN MODAL (ERROR) --}}
{{-- ========================= --}}

@if ($errors->any() && session('error_from') === 'edit_kelas')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        let id = "{{ session('edit_id') }}";

        $("#formEditKelas").attr("action", "/kelas/" + id);

        $("#modalEditKelas input[name='nama_kelas']").val("{{ old('nama_kelas') }}");
        $("#modalEditKelas input[name='angkatan']").val("{{ old('angkatan') }}");

        new bootstrap.Modal(document.getElementById("modalEditKelas")).show();
    });
</script>
@endif


@if ($errors->any() && session('error_from') === 'tambah_kelas')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        new bootstrap.Modal(document.getElementById("tambahKelas")).show();
    });
</script>
@endif

@endsection