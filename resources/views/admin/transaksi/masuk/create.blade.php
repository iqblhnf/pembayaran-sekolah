@extends('layouts.app')

@section('title', 'Tambah Transaksi Masuk')

@section('content')

<style>
    /* Layout 2 kolom untuk history */
    #history-content {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        /* 2 kolom */
        gap: 10px;
        /* jarak antar item */
    }

    .history-item {
        background: #ffffff;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 10px;
        border-left: 4px solid #0d6efd;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .history-title {
        font-weight: 600;
        font-size: 14px;
    }

    .history-date {
        font-size: 12px;
        color: #6c757d;
    }

    .history-nominal {
        font-size: 15px;
        font-weight: bold;
        color: #198754;
        margin-top: 5px;
    }

    .history-ket {
        font-size: 12px;
        color: #555;
        margin-top: 6px;
    }

    .jenis-item-card {
        background: #f7f7f7;
        border: 1px solid #e1e1e1;
        padding: 10px 12px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: 0.2s;
    }

    .jenis-item-card:hover {
        background: #efefef;
    }

    .jenis-item-card input {
        width: 18px;
        height: 18px;
        pointer-events: none;
        /* penting! agar klik card tidak klik dua kali */
    }

    .jenis-item-card span {
        font-size: 1rem;
    }

    #nominal-fields {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Tambah Transaksi Masuk</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('transaksi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tipe" value="masuk">

                        <div class="card-body">

                            {{-- JENIS PEMBAYARAN --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Pembayaran</label>

                                <div class="row">
                                    @foreach($jenis_pembayaran as $jp)
                                    <div class="col-md-4 col-6 mb-2">
                                        <label class="jenis-item-card" for="jp{{ $jp->id }}">
                                            <input type="checkbox"
                                                class="form-check-input jenis-pembayaran-checkbox"
                                                name="jenis_pembayaran_id[]"
                                                value="{{ $jp->id }}"
                                                id="jp{{ $jp->id }}"
                                                @if(old('jenis_pembayaran_id') && in_array($jp->id, old('jenis_pembayaran_id'))) checked @endif
                                            >
                                            <span>{{ $jp->nama_pembayaran }}</span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>

                                @if(session('error_from') === 'tambah_transaksi')
                                @error('jenis_pembayaran_id')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                                @endif
                            </div>

                            {{-- PREVIEW TERPILIH --}}
                            <div id="selected-payment-box" class="mt-2 mb-3" style="display:none;">
                                <label class="form-label fw-bold">Dipilih:</label>
                                <div id="selected-payment-list"
                                    class="border p-2 rounded"
                                    style="background:#f4f4f4; min-height:40px;">
                                </div>
                            </div>

                            <div class="row">
                                {{-- TANGGAL --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tanggal</label>
                                    <input type="datetime-local" name="tanggal" class="form-control" value="{{ old('tanggal', date('Y-m-d\TH:i')) }}">

                                    @if(session('error_from') === 'tambah_transaksi')
                                    @error('tanggal') <div class="text-danger">{{ $message }}</div> @enderror
                                    @endif
                                </div>

                                {{-- SISWA --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Siswa</label>
                                    <select name="siswa_id" id="select-siswa" class="form-select">
                                        <option value="">-- Tidak Ada --</option>
                                        @foreach($siswa as $s)
                                        <option value="{{ $s->id }}" {{ old('siswa_id') == $s->id ? 'selected' : '' }}>
                                            {{ $s->nama }}
                                        </option>
                                        @endforeach
                                    </select>

                                    @if(session('error_from') === 'tambah_transaksi')
                                    @error('siswa_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    @endif
                                </div>
                            </div>

                            {{-- HISTORY PEMBAYARAN --}}
                            <div id="history-box" class="mb-3" style="display:none;">
                                <h6 class="mb-2">Riwayat Pembayaran Siswa</h6>
                                <div id="history-content"
                                    class="border rounded p-2"
                                    style="max-height:200px; overflow-y:auto; background:#fafafa;">
                                    <small class="text-muted">Memuat data...</small>
                                </div>
                            </div>

                            {{-- NOMINAL PER JENIS PEMBAYARAN --}}
                            <div id="nominal-wrapper" class="mb-3" style="display:none;">
                                <label class="form-label fw-bold">Nominal per Jenis Pembayaran</label>

                                <div id="nominal-fields" class="row border rounded p-2" style="background:#fafafa;"></div>

                                {{-- TOTAL --}}
                                <div id="total-wrapper" class="mt-3" style="display:none;">
                                    <label class="form-label fw-bold">Total Nominal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" id="total-nominal-display" class="form-control" readonly>
                                    </div>
                                </div>

                                @if(session('error_from') === 'tambah_transaksi')
                                @error('nominal') <div class="text-danger">{{ $message }}</div> @enderror
                                @endif
                            </div>

                            {{-- METODE --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Metode Pembayaran</label>
                                <select class="form-select" name="metode">
                                    <option value="tunai" {{ old('metode')=='tunai' ? 'selected' : '' }}>Tunai</option>
                                    <option value="transfer" {{ old('metode')=='transfer' ? 'selected' : '' }}>Transfer</option>
                                </select>

                                @if(session('error_from') === 'tambah_transaksi')
                                @error('metode') <div class="text-danger">{{ $message }}</div> @enderror
                                @endif
                            </div>

                            {{-- KETERANGAN --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>

                                @if(session('error_from') === 'tambah_transaksi')
                                @error('keterangan') <div class="text-danger">{{ $message }}</div> @enderror
                                @endif
                            </div>

                        </div>

                        <div class="card-footer text-end">
                            <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Kembali</a>
                            <button class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- / Content -->
 
@if ($errors->any() && session('error_from') === 'tambah_transaksi')
<script>
    window.nominalErrors = @json($errors->getMessages());
</script>
@else
<script>
    window.nominalErrors = {};
</script>
@endif

<!-- SELECT SISWA -->
<script>
    $(function() {
        $('#select-siswa').select2({
            theme: 'bootstrap-5',
            placeholder: "-- Tidak Ada --",
            allowClear: true
        });
    });
</script>

<!-- AUTO GENERATE JENIS PEMBAYARAN -->
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const checkboxes = document.querySelectorAll('.jenis-pembayaran-checkbox');
        const box = document.getElementById('selected-payment-box');
        const listBox = document.getElementById('selected-payment-list');

        function updateSelectedList() {
            let selected = [];

            checkboxes.forEach(cb => {
                if (cb.checked) {
                    selected.push(cb.nextElementSibling.innerText.trim());
                }
            });

            if (selected.length > 0) {
                box.style.display = 'block';
                listBox.innerHTML = selected
                    .map(item => `<span class="badge bg-primary me-1">${item}</span>`)
                    .join('');
            } else {
                box.style.display = 'none';
                listBox.innerHTML = '';
            }
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedList));

        // tampilkan jika ada old() saat error
        updateSelectedList();
    });
</script>

<!-- SCRIPT: Generate nominal per jenis + hitung total + auto keterangan -->
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const checkboxes = document.querySelectorAll('.jenis-pembayaran-checkbox');
        const nominalWrapper = document.getElementById('nominal-wrapper');
        const nominalFields = document.getElementById('nominal-fields');
        const totalWrapper = document.getElementById('total-wrapper');
        const totalDisplay = document.getElementById('total-nominal-display');
        const keterangan = document.getElementById('keterangan');

        let keteranganAutoFilled = false;

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function parseNumber(str) {
            return Number(str.replace(/\./g, "")) || 0;
        }

        function generateKeterangan() {
            if (keteranganAutoFilled) return;

            let parts = [];

            checkboxes.forEach(cb => {
                if (cb.checked) {
                    const label = cb.nextElementSibling.innerText.trim();
                    const nominalInput = document.querySelector(`input[name="nominal[${cb.value}]"]`);
                    const nominal = nominalInput ? parseNumber(nominalInput.value) : 0;

                    if (nominal > 0) {
                        parts.push(`${label} (${formatNumber(nominal)})`);
                    } else {
                        parts.push(`${label}`);
                    }
                }
            });

            // Jika tidak ada checkbox yang dicentang → kosongkan keterangan
            if (parts.length === 0) {
                keterangan.value = "";
                return;
            }

            keterangan.value = "Pembayaran untuk: " + parts.join(", ");
        }

        function calculateTotal() {
            let total = 0;

            document.querySelectorAll('.input-nominal').forEach(inp => {
                total += parseNumber(inp.value);
            });

            if (total > 0) {
                totalWrapper.style.display = 'block';
                totalDisplay.value = formatNumber(total);
            } else {
                totalWrapper.style.display = 'none';
                totalDisplay.value = "";
            }
        }

        let savedNominal = {};

        function saveCurrentNominal() {
            document.querySelectorAll('.input-nominal').forEach(inp => {
                savedNominal[inp.name] = inp.value;
            });
        }

        function generateNominalInputs() {

            // --- Simpan semua nominal yang sudah ada sebelum di-refresh
            saveCurrentNominal();

            const selected = [...checkboxes].filter(cb => cb.checked);

            if (selected.length === 0) {
                nominalWrapper.style.display = 'none';
                nominalFields.innerHTML = '';
                totalWrapper.style.display = 'none';
                return;
            }

            nominalWrapper.style.display = 'block';

            let html = '';
            const oldValue = @json(old('nominal', []));

            selected.forEach(cb => {
                const label = cb.nextElementSibling.innerText.trim();
                const id = cb.value;

                const key = `nominal[${id}]`;
                const value =
                    savedNominal[key] // ← PRIORITAS: nilai yang pernah diinput user
                    ??
                    oldValue[id] // nilai old() saat validasi error
                    ??
                    ''; // default kosong

                let errorKey = `nominal.${id}`;
                let errorHtml = "";

                if (window.nominalErrors && window.nominalErrors[errorKey]) {
                    errorHtml = `<div class="text-danger mt-1">${window.nominalErrors[errorKey][0]}</div>`;
                }

                html += `
<div class="col-md-4 col-sm-6 col-12 mb-2">
    <label class="form-label">Nominal ${label}</label>
    <div class="input-group">
        <span class="input-group-text">Rp.</span>
        <input 
            type="text" 
            class="form-control input-nominal format-nominal" 
            name="nominal[${id}]" 
            value="${value}"
            autocomplete="off"
        >
    </div>
    ${errorHtml}
</div>
`;
            });

            nominalFields.innerHTML = html;

            // Pasang listener baru
            document.querySelectorAll('.input-nominal').forEach(inp => {
                inp.addEventListener('keyup', function() {
                    calculateTotal();
                    generateKeterangan();
                });
            });

            calculateTotal();
            generateKeterangan();
        }

        checkboxes.forEach(cb => cb.addEventListener('change', function() {
            generateNominalInputs();
            generateKeterangan(); // <=== FIX UTAMA
        }));

        keterangan.addEventListener('input', function() {
            keteranganAutoFilled = true;
        });

        generateNominalInputs();
    });
</script>

<!-- AUTO HISTORY -->
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // === INIT SELECT2 DALAM MODAL ===
        $('#select-siswa').select2({
            theme: "bootstrap-5",
        });

        const historyBox = document.getElementById("history-box");
        const historyContent = document.getElementById("history-content");

        // === FUNGSI AMBIL HISTORY ===
        function loadHistory(siswaID) {

            if (!siswaID) {
                historyBox.style.display = "none";
                return;
            }

            historyBox.style.display = "block";
            historyContent.innerHTML = "<small class='text-muted'>Memuat data...</small>";

            fetch("{{ url('/transaksi/history') }}/" + siswaID)
                .then(res => res.json())
                .then(data => {
                    historyContent.innerHTML = data.html;
                })
                .catch(err => {
                    historyContent.innerHTML = "<small class='text-danger'>Gagal memuat data.</small>";
                });
        }

        // === PENTING: EVENT SELECT2 ===
        $('#select-siswa').on('change select2:select', function() {
            loadHistory($(this).val());
        });

    });
</script>

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

@endsection