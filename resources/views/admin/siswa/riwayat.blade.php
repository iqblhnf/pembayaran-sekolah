@extends('layouts.app')

@section('title', 'Riwayat Pembayaran')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">
        Riwayat Pembayaran Masuk - {{ $siswa->nama }}
    </h4>

    <div class="card p-3">

        {{-- BIODATA SISWA --}}
        <div class="mb-3">
            <strong>NIS:</strong> {{ $siswa->nis }} <br>
            <strong>Kelas:</strong> {{ $siswa->kelas->nama_kelas }} <br>
            <strong>Angkatan:</strong> {{ $siswa->kelas->angkatan }} <br>
            <strong>Orang Tua:</strong> {{ $siswa->nama_ortu ?? '-' }} <br>
            <strong>Telp Orang Tua:</strong> {{ $siswa->telp_ortu ?? '-' }} <br>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tableRiwayat">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Kode Transaksi</th>
                        <th>Jenis Pembayaran</th>
                        <th>Nominal</th>
                        <th>Metode</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y H:i') }}</td>
                        <td>{{ $item->kode_transaksi }}</td>
                        @php
                        $jenis_ids = json_decode($item->jenis_pembayaran_id, true);
                        $jenisList = \App\Models\JenisPembayaran::whereIn('id', $jenis_ids)->pluck('nama_pembayaran')->toArray();
                        @endphp

                        <td>{{ implode(', ', $jenisList) }}</td>
                        <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($item->metode) ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada pembayaran masuk</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="text-end">
            <a href="{{ route('siswa.index') }}" class="btn btn-secondary mt-3">Kembali</a>
        </div>

    </div>
</div>

@endsection