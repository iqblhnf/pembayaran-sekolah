@extends('layouts.app')

@section('title', 'Siswa')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Siswa</h4>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    {{-- PREVIEW IMPORT EXCEL --}}
    @if(isset($preview) && count($preview) > 0)
    <div class="card mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Preview Import Siswa</h5>
        </div>

        <div class="card-body">

            {{-- ERROR BARIS --}}
            @if(isset($errors_preview) && count($errors_preview) > 0)
            <div class="alert alert-danger">
                <strong>Beberapa error ditemukan:</strong><br>
                @foreach($errors_preview as $e)
                - {{ $e }} <br>
                @endforeach
            </div>
            @endif

            <form action="{{ route('siswa.import.confirm') }}" method="POST" id="confirmForm">
                @csrf
                <input type="hidden" name="file_path" value="{{ $file_path }}">

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Angkatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($preview as $p)
                        <tr>
                            <td>
                                @if($p['mode'] == 'insert')
                                <span class="badge bg-success">Tambah</span>
                                @else
                                <span class="badge bg-warning">Update</span>
                                @endif
                            </td>
                            <td>{{ $p['row']['nis'] }}</td>
                            <td>{{ $p['row']['nama'] }}</td>
                            <td>{{ $p['row']['kelas'] }}</td>
                            <td>{{ $p['row']['angkatan'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <button class="btn btn-primary mt-3">Konfirmasi Import</button>
                <a href="{{ route('siswa.index') }}" class="btn btn-secondary mt-3">Batal</a>
            </form>

        </div>
    </div>
    @endif

    <!-- Responsive Table -->
    <div class="card p-2">
        <h5 class="card-header d-flex justify-content-between">

            <!-- Tombol kiri -->
            <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahSiswa">
                    Tambah Siswa
                </button>
            </div>

            <!-- Tombol kanan -->
            <div>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importSiswa">
                    <i class='bx bxs-file-import me-1'></i>
                    Import Excel
                </button>

                <a href="{{ route('siswa.download.template') }}" class="btn btn-info">
                    <i class='bx bxs-download me-1'></i>
                    Download Template
                </a>
            </div>

        </h5>

        <div class="table-responsive text-nowrap">
            <table class="table table-bordered" id="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Alamat</th>
                        <th>Orang Tua</th>
                        <th>Telepon Orang Tua</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <!--/ Responsive Table -->
</div>

<!-- Modal -->
<div class="modal fade" id="tambahSiswa" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5">Tambah Siswa</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('siswa.store') }}" method="post">
                @csrf

                <div class="modal-body">

                    {{-- PILIH KELAS --}}
                    <div class="mb-3">
                        <label class="col-form-label">Kelas</label>
                        <select name="kelas_id" class="form-control">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                            <option value="{{ $k->id }}"
                                {{ session('error_from') === 'tambah_siswa' && old('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                            @endforeach
                        </select>

                        @if (session('error_from') === 'tambah_siswa')
                        @error('kelas_id')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- NIS --}}
                    <div class="mb-3">
                        <label class="col-form-label">NIS</label>
                        <input type="text" class="form-control" name="nis"
                            value="{{ session('error_from') === 'tambah_siswa' ? old('nis') : '' }}">

                        @if (session('error_from') === 'tambah_siswa')
                        @error('nis')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- NAMA --}}
                    <div class="mb-3">
                        <label class="col-form-label">Nama Siswa</label>
                        <input type="text" class="form-control" name="nama"
                            value="{{ session('error_from') === 'tambah_siswa' ? old('nama') : '' }}">

                        @if (session('error_from') === 'tambah_siswa')
                        @error('nama')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- ALAMAT --}}
                    <div class="mb-3">
                        <label class="col-form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3">
                        {{ session('error_from') === 'tambah_siswa' ? old('alamat') : '' }}</textarea>

                        @if (session('error_from') === 'tambah_siswa')
                        @error('alamat')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- NAMA ORTU --}}
                    <div class="mb-3">
                        <label class="col-form-label">Nama Orang Tua</label>
                        <input type="text" class="form-control" name="nama_ortu"
                            value="{{ session('error_from') === 'tambah_siswa' ? old('nama_ortu') : '' }}">

                        @if (session('error_from') === 'tambah_siswa')
                        @error('nama_ortu')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- TELEPON ORANG TUA --}}
                    <div class="mb-3">
                        <label class="col-form-label">Telepon Orang Tua</label>
                        <input type="text" class="form-control" name="telp_ortu"
                            value="{{ session('error_from') === 'tambah_siswa' ? old('telp_ortu') : '' }}">

                        @if (session('error_from') === 'tambah_siswa')
                        @error('telp_ortu')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- STATUS --}}
                    <div class="mb-3">
                        <label class="col-form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="aktif"
                                {{ session('error_from') === 'tambah_siswa' && old('status') == 'aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>
                            <option value="lulus"
                                {{ session('error_from') === 'tambah_siswa' && old('status') == 'lulus' ? 'selected' : '' }}>
                                Lulus
                            </option>
                            <option value="keluar"
                                {{ session('error_from') === 'tambah_siswa' && old('status') == 'keluar' ? 'selected' : '' }}>
                                Keluar
                            </option>
                        </select>

                        @if (session('error_from') === 'tambah_siswa')
                        @error('status')
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

<!-- ========================= -->
<!-- MODAL EDIT SISWA -->
<!-- ========================= -->
<div class="modal fade" id="modalEditSiswa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formEditSiswa" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- PILIH KELAS --}}
                    <div class="mb-3">
                        <label class="col-form-label">Kelas</label>
                        <select name="kelas_id" class="form-control">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>

                        @if (session('error_from') === 'edit_siswa')
                        @error('kelas_id')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @endif
                    </div>

                    {{-- NIS --}}
                    <div class="mb-3">
                        <label class="col-form-label">NIS</label>
                        <input type="text" class="form-control" name="nis">
                        @if (session('error_from') === 'edit_siswa')
                        @error('nis') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- NAMA --}}
                    <div class="mb-3">
                        <label class="col-form-label">Nama Siswa</label>
                        <input type="text" class="form-control" name="nama">
                        @if (session('error_from') === 'edit_siswa')
                        @error('nama') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- ALAMAT --}}
                    <div class="mb-3">
                        <label class="col-form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3"></textarea>
                        @if (session('error_from') === 'edit_siswa')
                        @error('alamat') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- NAMA ORTU --}}
                    <div class="mb-3">
                        <label class="col-form-label">Nama Orang Tua</label>
                        <input type="text" class="form-control" name="nama_ortu">
                        @if (session('error_from') === 'edit_siswa')
                        @error('nama_ortu') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- TELEPON ORTU --}}
                    <div class="mb-3">
                        <label class="col-form-label">Telepon Orang Tua</label>
                        <input type="text" class="form-control" name="telp_ortu">
                        @if (session('error_from') === 'edit_siswa')
                        @error('telp_ortu') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- STATUS --}}
                    <div class="mb-3">
                        <label class="col-form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="aktif">Aktif</option>
                            <option value="lulus">Lulus</option>
                            <option value="keluar">Keluar</option>
                        </select>
                        @if (session('error_from') === 'edit_siswa')
                        @error('status') <div class="text-danger">{{ $message }}</div> @enderror
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

<!-- Modal Import -->
<div class="modal fade" id="importSiswa" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('siswa.import.preview') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Import Data Siswa dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label class="form-label">Pilih File Excel</label>
                <input type="file" name="file" class="form-control" required>

                <p class="mt-2 text-muted">
                    Format kolom wajib:
                    <br> nama, nis, kelas, angkatan, alamat, nama_ortu, telp_ortu, status
                </p>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Import</button>
            </div>
        </form>
    </div>
</div>

<script>
    // DataTables server-side (contoh)
    $(function() {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('siswa.data') }}",
            columns: [{
                    data: 0
                }, // #
                {
                    data: 1
                }, // nama
                {
                    data: 2
                }, // nis
                {
                    data: 3
                }, // kelas
                {
                    data: 4
                }, // alamat
                {
                    data: 5
                }, // ortu
                {
                    data: 6
                }, // telp
                {
                    data: 7
                }, // status badge
                {
                    data: 8,
                    orderable: false,
                    searchable: false
                }, // aksi
            ]
        });
    });

    // TOMBOL EDIT → AMBIL DATA JSON
    $(document).on("click", ".btn-edit", function() {
        let id = $(this).data("id");

        $.get("/siswa/" + id, function(res) {

            $("#formEditSiswa").attr("action", "/siswa/" + id);

            $("#modalEditSiswa select[name='kelas_id']").val(res.kelas_id);
            $("#modalEditSiswa input[name='nis']").val(res.nis);
            $("#modalEditSiswa input[name='nama']").val(res.nama);
            $("#modalEditSiswa textarea[name='alamat']").val(res.alamat);
            $("#modalEditSiswa input[name='nama_ortu']").val(res.nama_ortu);
            $("#modalEditSiswa input[name='telp_ortu']").val(res.telp_ortu);
            $("#modalEditSiswa select[name='status']").val(res.status);

            new bootstrap.Modal(document.getElementById("modalEditSiswa")).show();
        });
    });
</script>

{{-- ========================= --}}
{{-- AUTO OPEN MODAL EDIT --}}
{{-- ========================= --}}
@if ($errors->any() && session('error_from') === 'edit_siswa')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        let id = "{{ session('edit_id') }}";

        // Set action ke ID yang benar
        $("#formEditSiswa").attr("action", "/siswa/" + id);

        // Isi ulang form edit dengan old()
        $("#modalEditSiswa select[name='kelas_id']").val("{{ old('kelas_id') }}");
        $("#modalEditSiswa input[name='nis']").val("{{ old('nis') }}");
        $("#modalEditSiswa input[name='nama']").val("{{ old('nama') }}");
        $("#modalEditSiswa textarea[name='alamat']").val(`{{ old('alamat') }}`);
        $("#modalEditSiswa input[name='nama_ortu']").val("{{ old('nama_ortu') }}");
        $("#modalEditSiswa input[name='telp_ortu']").val("{{ old('telp_ortu') }}");
        $("#modalEditSiswa select[name='status']").val("{{ old('status') }}");

        // Tampilkan modal edit
        new bootstrap.Modal(document.getElementById("modalEditSiswa")).show();
    });
</script>
@endif

{{-- ========================= --}}
{{-- AUTO OPEN MODAL TAMBAH --}}
{{-- ========================= --}}
@if ($errors->any() && session('error_from') === 'tambah_siswa')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        new bootstrap.Modal(document.getElementById("tambahSiswa")).show();
    });
</script>
@endif

@endsection