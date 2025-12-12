@extends('layouts.app')

@section('title', 'Transaksi Masuk')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4">Transaksi Masuk</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Card -->
    <div class="card p-2">
        <h5 class="card-header">
            <a href="{{ route('transaksi.masuk.create') }}" class="btn btn-primary">Tambah Transaksi</a>
        </h5>

        <div class="table-responsive text-nowrap">
            <table class="table table-bordered" id="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Kode Transaksi</th>
                        <th>Jenis Pembayaran</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Nominal</th>
                        <!-- <th>Metode</th> -->
                        <!-- <th>Keterangan</th> -->
                        <th>Cetak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    $(function() {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('transaksi.masuk.data') }}",
            columns: [{
                    data: 0,
                    name: 'index'
                },
                {
                    data: 1,
                    name: 'tanggal'
                },
                {
                    data: 2,
                    name: 'kode_transaksi'
                },
                {
                    data: 3,
                    name: 'jenis_pembayaran'
                },
                {
                    data: 4,
                    name: 'siswa'
                },
                {
                    data: 5,
                    name: 'kelas'
                },
                {
                    data: 6,
                    name: 'nominal'
                },
                {
                    data: 7,
                    orderable: false,
                    searchable: false
                },
                {
                    data: 8,
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });
</script>

@endsection