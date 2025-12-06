@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- ============================
         FILTER TAHUN & TIPE
    ============================= -->
    <div class="d-flex gap-2 justify-content-end mb-3">
        <form method="GET" class="d-flex gap-2">

            <!-- Filter Tahun -->
            <select name="tahun" onchange="this.form.submit()" class="form-select w-auto">
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                    {{ $y }}
                </option>
                @endfor
            </select>

            <!-- Filter Jenis Transaksi -->
            <select name="tipe" onchange="this.form.submit()" class="form-select w-auto">
                <option value="gabungan" {{ $filterTipe=='gabungan' ? 'selected':'' }}>Gabungan</option>
                <option value="masuk" {{ $filterTipe=='masuk' ? 'selected':'' }}>Pemasukan</option>
                <option value="keluar" {{ $filterTipe=='keluar' ? 'selected':'' }}>Pengeluaran</option>
            </select>
        </form>
    </div>

    <!-- ============================
         SUMMARY CARD
    ============================= -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>Total Pemasukan</h5>
                <h3 class="text-success">Rp {{ number_format($totalMasuk,0,',','.') }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>Total Pengeluaran</h5>
                <h3 class="text-danger">Rp {{ number_format($totalKeluar,0,',','.') }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>Saldo Akhir</h5>
                <h3 class="text-primary">Rp {{ number_format($saldoAkhir,0,',','.') }}</h3>
            </div>
        </div>
    </div>

    <!-- ============================
         GRAFIK BULANAN
    ============================= -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5>Grafik Bulanan ({{ ucfirst($filterTipe) }})</h5>
            <div id="monthlyChart" style="min-height: 300px;"></div>
        </div>
    </div>

    <!-- ============================
         GRAFIK MINGGUAN
    ============================= -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5>Grafik Mingguan ({{ ucfirst($filterTipe) }})</h5>
            <div id="weeklyChart" style="min-height: 300px;"></div>
        </div>
    </div>

    <div class="row">
        <!-- ============================
             PER KELAS
        ============================= -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Per Kelas</h5>
                    <div id="kelasChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>

        <!-- ============================
             PER METODE
        ============================= -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5>Per Metode Pembayaran</h5>
                    <div id="metodeChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================
         PER SISWA
    ============================= -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5>Per Siswa (Top 10)</h5>
            <div id="siswaChart" style="min-height: 300px;"></div>
        </div>
    </div>

    <!-- ============================
         JENIS TRANSAKSI
    ============================= -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5>Jenis Transaksi</h5>
            <div id="jenisChart" style="min-height: 300px;"></div>
        </div>
    </div>

</div>
@endsection



@push('scripts')
<script>
    /* =========================================================
       BULANAN — 1 atau 2 series tergantung filter tipe
    ========================================================== */
    let monthlySeries = [];

    @if($filterTipe === 'gabungan')
    monthlySeries = [{
            name: "Pemasukan",
            data: @json($monthMasukData)
        },
        {
            name: "Pengeluaran",
            data: @json($monthKeluarData)
        }
    ];
    @elseif($filterTipe === 'masuk')
    monthlySeries = [{
        name: "Pemasukan",
        data: @json($monthMasukData)
    }];
    @else
    monthlySeries = [{
        name: "Pengeluaran",
        data: @json($monthKeluarData)
    }];
    @endif

    new ApexCharts(document.querySelector("#monthlyChart"), {
        chart: {
            type: 'area',
            height: 300
        },
        series: monthlySeries,
        xaxis: {
            categories: @json($monthLabels)
        },
        stroke: {
            curve: 'smooth'
        },
        colors: ['#28a745', '#dc3545']
    }).render();



    /* =========================================================
       MINGGUAN
    ========================================================== */
    new ApexCharts(document.querySelector("#weeklyChart"), {
        chart: {
            type: 'bar',
            height: 300
        },
        series: [{
            name: "{{ ucfirst($filterTipe) }}",
            data: @json($weeklyData)
        }],
        xaxis: {
            categories: @json($weeklyLabels)
        },
        colors: ['#0d6efd']
    }).render();



    /* =========================================================
       PER KELAS
    ========================================================== */
    new ApexCharts(document.querySelector("#kelasChart"), {
        chart: {
            type: 'bar',
            height: 300
        },
        series: [{
            name: "Total",
            data: @json($kelasTotals)
        }],
        xaxis: {
            categories: @json($kelasLabels)
        },
        colors: ['#6610f2']
    }).render();



    /* =========================================================
       PER METODE
    ========================================================== */
    new ApexCharts(document.querySelector("#metodeChart"), {
        chart: {
            type: 'pie',
            height: 300
        },
        series: @json($metodeTotals),
        labels: @json($metodeLabels),
        colors: ['#20c997', '#fd7e14']
    }).render();



    /* =========================================================
       PER SISWA
    ========================================================== */
    new ApexCharts(document.querySelector("#siswaChart"), {
        chart: {
            type: 'bar',
            height: 300
        },
        series: [{
            name: "Total",
            data: @json($siswaTotals)
        }],
        xaxis: {
            categories: @json($siswaLabels)
        },
        colors: ['#17a2b8']
    }).render();



    /* =========================================================
       JENIS TRANSAKSI
    ========================================================== */
    new ApexCharts(document.querySelector("#jenisChart"), {
        chart: {
            type: 'donut',
            height: 300
        },
        series: @json($jenisTotals),
        labels: @json($jenisLabels),
        colors: ['#0dcaf0', '#6610f2', '#ffc107', '#198754', '#dc3545']
    }).render();
</script>
@endpush