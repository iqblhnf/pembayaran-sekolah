@extends('layouts.app')

@section('title', 'Transaksi Keluar')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">Transaksi Keluar</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Card -->
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
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Deskripsi</th>
                        <th>Nominal</th>
                        <th>Metode</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($transaksi as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->siswa->nama ?? '-' }}</td>
                        <td>{{ $item->siswa->kelas->nama_kelas ?? '-' }}</td>
                        <td>{{ $item->deskripsi }}</td>
                        <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($item->metode) ?? '-' }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>

                        <td>
                            <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editTransaksi{{ $item->id }}">
                                Edit
                            </button>

                            <a href="{{ route('transaksi.kwitansi', $item->id) }}"
                                class="btn btn-info btn-sm"
                                target="_blank">
                                Cetak
                            </a>

                            <form action="{{ route('transaksi.destroy', $item->id) }}"
                                class="d-inline"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin hapus?')">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>

                    <!-- ========================= -->
                    <!-- Modal Edit Transaksi -->
                    <!-- ========================= -->
                    <div class="modal fade" id="editTransaksi{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Transaksi</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <form action="{{ route('transaksi.update', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="tipe" value="keluar">

                                    <div class="modal-body">

                                        {{-- TANGGAL --}}
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal</label>
                                            <input type="date" class="form-control" name="tanggal"
                                                value="{{ old('tanggal', $item->tanggal) }}">

                                            @if(session('error_from') === 'edit_transaksi' && session('edit_id') == $item->id)
                                            @error('tanggal') <div class="text-danger">{{ $message }}</div> @enderror
                                            @endif
                                        </div>

                                        {{-- SISWA --}}
                                        <div class="mb-3">
                                            <label class="form-label">Siswa (Opsional)</label>
                                            <select name="siswa_id" class="form-control">
                                                <option value="">-- Tidak Ada --</option>
                                                @foreach($siswa as $s)
                                                <option value="{{ $s->id }}"
                                                    {{ old('siswa_id', $item->siswa_id) == $s->id ? 'selected' : '' }}>
                                                    {{ $s->nama }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @if(session('error_from') === 'edit_transaksi' && session('edit_id') == $item->id)
                                            @error('siswa_id') <div class="text-danger">{{ $message }}</div> @enderror
                                            @endif
                                        </div>

                                        {{-- DESKRIPSI --}}
                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi</label>
                                            <textarea class="form-control" name="deskripsi" rows="3">{{ old('deskripsi', $item->deskripsi) }}</textarea>

                                            @if(session('error_from') === 'edit_transaksi' && session('edit_id') == $item->id)
                                            @error('deskripsi') <div class="text-danger">{{ $message }}</div> @enderror
                                            @endif
                                        </div>

                                        {{-- NOMINAL --}}
                                        <div class="mb-3">
                                            <label class="form-label">Nominal</label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="basic-addon1">Rp.</span>
                                                <input type="text" class="form-control format-nominal" name="nominal"
                                                    value="{{ old('nominal', number_format($item->nominal, 0, ',', '.')) }}">
                                            </div>

                                            @if(session('error_from') === 'edit_transaksi' && session('edit_id') == $item->id)
                                            @error('nominal') <div class="text-danger">{{ $message }}</div> @enderror
                                            @endif
                                        </div>

                                        {{-- METODE --}}
                                        <div class="mb-3">
                                            <label class="form-label">Metode</label>
                                            <select class="form-control" name="metode">
                                                <option value="">-- Pilih --</option>
                                                <option value="tunai" {{ old('metode', $item->metode) == 'tunai' ? 'selected' : '' }}>Tunai</option>
                                                <option value="transfer" {{ old('metode', $item->metode) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                            </select>

                                            @if(session('error_from') === 'edit_transaksi' && session('edit_id') == $item->id)
                                            @error('metode') <div class="text-danger">{{ $message }}</div> @enderror
                                            @endif
                                        </div>

                                        {{-- KETERANGAN --}}
                                        <div class="mb-3">
                                            <label class="form-label">Keterangan</label>
                                            <textarea class="form-control" name="keterangan" rows="3">{{ old('keterangan', $item->keterangan) }}</textarea>

                                            @if(session('error_from') === 'edit_transaksi' && session('edit_id') == $item->id)
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
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ================================= -->
<!-- Modal Tambah Transaksi -->
<!-- ================================= -->
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
                        <input type="date" class="form-control" name="tanggal" value="{{ old('tanggal') }}">

                        @if(session('error_from') === 'tambah_transaksi')
                        @error('tanggal') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- SISWA --}}
                    <div class="mb-3">
                        <label class="form-label">Siswa (Opsional)</label>
                        <select name="siswa_id" class="form-control">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($siswa as $s)
                            <option value="{{ $s->id }}" {{ old('siswa_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->nama }}
                            </option>
                            @endforeach
                        </select>

                        @if(session('error_from') === 'tambah_transaksi')
                        @error('siswa_id') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>

                        @if(session('error_from') === 'tambah_transaksi')
                        @error('deskripsi') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- NOMINAL --}}
                    <div class="mb-3">
                        <label class="form-label">Nominal</label>
                        <div class="input-group">
                            <span class="input-group-text" id="basic-addon1">Rp.</span>
                            <input type="text" class="form-control format-nominal" name="nominal"
                                value="{{ old('nominal') }}">
                        </div>

                        @if(session('error_from') === 'tambah_transaksi')
                        @error('nominal') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- METODE --}}
                    <div class="mb-3">
                        <label class="form-label">Metode</label>
                        <select class="form-control" name="metode">
                            <option value="">-- Pilih --</option>
                            <option value="tunai" {{ old('metode')=='tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="transfer" {{ old('metode')=='transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>

                        @if(session('error_from') === 'tambah_transaksi')
                        @error('metode') <div class="text-danger">{{ $message }}</div> @enderror
                        @endif
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>

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

<script>
    document.addEventListener("input", function(e) {
        if (e.target.classList.contains("format-nominal")) {
            let value = e.target.value.replace(/\D/g, ""); // Hapus semua kecuali angka

            if (value) {
                e.target.value = new Intl.NumberFormat('id-ID').format(value);
            } else {
                e.target.value = "";
            }
        }
    });
</script>

{{-- ======================================== --}}
{{-- AUTO OPEN MODAL --}}
{{-- ======================================== --}}
@if ($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {

        @if(session('error_from') === 'tambah_transaksi')
        new bootstrap.Modal(document.getElementById("tambahTransaksi")).show();
        @endif

        @if(session('error_from') === 'edit_transaksi')
        new bootstrap.Modal(
            document.getElementById("editTransaksi{{ session('edit_id') }}")
        ).show();
        @endif

    });
</script>

@endif

@endsection