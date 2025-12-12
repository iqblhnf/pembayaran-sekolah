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
         GRAFIK BULANAN SAJA
    ============================= -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5>Grafik Bulanan ({{ ucfirst($filterTipe) }})</h5>
            <div id="monthlyChart" style="min-height: 350px;"></div>
        </div>
    </div>

    {{-- Semua grafik lain DISABLED sesuai permintaan --}}
    {{--
        <div class="card"> GRAFIK MINGGUAN </div>
        <div class="card"> PER KELAS </div>
        <div class="card"> PER METODE </div>
        <div class="card"> PER SISWA </div>
        <div class="card"> JENIS TRANSAKSI </div>
    --}}

</div>
@endsection


@push('scripts')
<script>
    /* =========================================================
       TENTUKAN SERIES BULANAN
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

    /* =========================================================
       WARNA DINAMIS
    ========================================================== */
    let monthlyColors = [];

    @if($filterTipe === 'masuk')
    monthlyColors = ['#28a745']; // hijau
    @elseif($filterTipe === 'keluar')
    monthlyColors = ['#dc3545']; // merah
    @else
    monthlyColors = ['#28a745', '#dc3545']; // gabungan: masuk + keluar
    @endif


    /* =========================================================
       RENDER GRAFIK BULANAN
    ========================================================== */
    new ApexCharts(document.querySelector("#monthlyChart"), {
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: true
            }
        },
        series: monthlySeries,
        colors: monthlyColors,
        xaxis: {
            categories: @json($monthLabels)
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        fill: {
            type: "gradient",
            gradient: {
                shadeIntensity: 0.4,
                opacityFrom: 0.5,
                opacityTo: 0.1
            }
        }
    }).render();
</script>
@endpush