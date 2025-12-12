@extends('layouts.app')

@section('title', 'Transaksi Keluar')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">Transaksi Keluar</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card p-2">
        <h5 class="card-header">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahTransaksi">
                Tambah Transaksi
            </button>
        </h5>

        <div class="table-responsive text-nowrap">
            <table class="table table-bordered" id="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Kode Transaksi</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                        <th>Cetak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

<!-- ======================== -->
<!--  MODAL TAMBAH TRANSAKSI -->
<!-- ======================== -->
<div class="modal fade" id="tambahTransaksi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('transaksi.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tipe" value="keluar">

                <div class="modal-body">

                    {{-- TANGGAL --}}
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="datetime-local"
                            class="form-control"
                            name="tanggal"
                            value="{{ session('error_from') === 'tambah_transaksi' ? old('tanggal') : date('Y-m-d\TH:i') }}">

                        @if(session('error_from') === 'tambah_transaksi')
                        @error('tanggal') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- NOMINAL --}}
                    <div class="mb-3">
                        <label class="form-label">Nominal</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="text"
                                class="form-control format-nominal"
                                name="nominal"
                                value="{{ session('error_from') === 'tambah_transaksi' ? old('nominal') : '' }}">
                        </div>

                        @if(session('error_from') === 'tambah_transaksi')
                        @error('nominal') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3">{{ session('error_from') === 'tambah_transaksi' ? old('keterangan') : '' }}</textarea>

                        @if(session('error_from') === 'tambah_transaksi')
                        @error('keterangan') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- ======================== -->
<!--  MODAL EDIT TRANSAKSI   -->
<!-- ======================== -->
<div class="modal fade" id="modalEditTransaksi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="formEditTransaksi" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="tipe" value="keluar">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Transaksi Keluar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- TANGGAL --}}
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input type="datetime-local" class="form-control" name="tanggal">

                        @if(session('error_from') === 'edit_transaksi')
                        @error('tanggal') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- NOMINAL --}}
                    <div class="mb-3">
                        <label class="form-label">Nominal</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control format-nominal" name="nominal">
                        </div>

                        @if(session('error_from') === 'edit_transaksi')
                        @error('nominal') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- KETERANGAN --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"></textarea>

                        @if(session('error_from') === 'edit_transaksi')
                        @error('keterangan') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- ======================== -->
<!--  S C R I P T      -->
<!-- ======================== -->
<script>
    $(function() {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('transaksi.keluar.data') }}",
            columns: [{
                    data: 0
                }, {
                    data: 1
                }, {
                    data: 2
                },
                {
                    data: 3
                }, {
                    data: 4
                }, {
                    data: 5
                },
                {
                    data: 6,
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });


    // ========================================
    // BUTTON EDIT → AMBIL DATA JSON
    // ========================================
    $(document).on("click", ".btn-edit", function(e) {
        e.preventDefault();
        let id = $(this).data("id");

        $.get("/transaksi/keluar/" + id + "/show", function(res) {

            $("#formEditTransaksi").attr("action", "/transaksi/" + id);

            $("#modalEditTransaksi input[name='tanggal']")
                .val(res.tanggal.replace(" ", "T"));

            $("#modalEditTransaksi input[name='nominal']")
                .val(new Intl.NumberFormat('id-ID').format(res.nominal));

            $("#modalEditTransaksi textarea[name='keterangan']")
                .val(res.keterangan);

            new bootstrap.Modal(document.getElementById("modalEditTransaksi")).show();
        });
    });


    // === Format Nominal ===
    document.addEventListener("input", function(e) {
        if (e.target.classList.contains("format-nominal")) {
            let value = e.target.value.replace(/\D/g, "");
            e.target.value = value ? new Intl.NumberFormat('id-ID').format(value) : "";
        }
    });
</script>


{{-- ======================================== --}}
{{-- AUTO OPEN MODAL SAAT ERROR --}}
{{-- ======================================== --}}
@if ($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // === ERROR TAMBAH ===
        @if(session('error_from') === 'tambah_transaksi')
        new bootstrap.Modal(document.getElementById("tambahTransaksi")).show();
        @endif

        // === ERROR EDIT ===
        @if(session('error_from') === 'edit_transaksi')

        $("#modalEditTransaksi input[name='tanggal']").val("{{ old('tanggal') }}");
        $("#modalEditTransaksi input[name='nominal']").val("{{ old('nominal') }}");
        $("#modalEditTransaksi textarea[name='keterangan']").val(`{{ old('keterangan') }}`);

        new bootstrap.Modal(document.getElementById("modalEditTransaksi")).show();
        @endif

    });
</script>
@endif

@endsection