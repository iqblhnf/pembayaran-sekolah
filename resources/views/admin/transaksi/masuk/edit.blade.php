@extends('layouts.app')

@section('title', 'Edit Transaksi Masuk')

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

    <h4 class="fw-bold py-3 mb-4">Edit Transaksi Masuk</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">

                    <form action="{{ route('transaksi.update', $transaksi->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="tipe" value="masuk">

                        <div class="card-body">

                            {{-- ============================
                                JENIS PEMBAYARAN
                            ============================ --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Pembayaran</label>

                                <div class="row">
                                    @foreach($jenis_pembayaran as $jp)

                                    @php
                                        // Status error dari controller
                                        $isError = session('error_from') === 'edit_transaksi';

                                        // Old value (array / null)
                                        $oldJenis = old('jenis_pembayaran_id');

                                        // Data transaksi lama (array nominal tersimpan)
                                        $inDb = isset($detail[$jp->id]);

                                        /*
                                            ============================
                                            LOGIKA CHECKED FINAL
                                            ============================

                                            1) Jika error & old ada → pakai old
                                            2) Jika error & old kosong → kembali ke data transaksi
                                            3) Jika tidak error → pakai data transaksi
                                        */
                                        if ($isError) {
                                            if (is_array($oldJenis)) {
                                                $checked = in_array($jp->id, $oldJenis);
                                            } else {
                                                $checked = $inDb; // old kosong → pakai data transaksi
                                            }
                                        } else {
                                            $checked = $inDb; // halaman normal → pakai data transaksi
                                        }
                                    @endphp

                                    <div class="col-md-4 col-6 mb-2">
                                        <label class="jenis-item-card" for="jp{{ $jp->id }}">
                                            <input type="checkbox"
                                                name="jenis_pembayaran_id[]"
                                                class="form-check-input jenis-pembayaran-checkbox"
                                                value="{{ $jp->id }}"
                                                id="jp{{ $jp->id }}"
                                                {{ $checked ? 'checked' : '' }}
                                            >
                                            <span>{{ $jp->nama_pembayaran }}</span>
                                        </label>
                                    </div>

                                    @endforeach
                                </div>

                                @if(session('error_from') === 'edit_transaksi')
                                    @error('jenis_pembayaran_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>


                            {{-- ============================
             PREVIEW TERPILIH
        ============================= --}}
                            <div id="selected-payment-box" class="mt-2 mb-3" style="display:none;">
                                <label class="form-label fw-bold">Dipilih:</label>
                                <div id="selected-payment-list" class="border rounded p-2" style="background:#f4f4f4; min-height:40px;"></div>
                            </div>


                            {{-- ============================
             TANGGAL & SISWA
        ============================= --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Tanggal</label>
                                    <input type="datetime-local" class="form-control"
                                        name="tanggal"
                                        value="{{ old('tanggal', $transaksi->tanggal) }}" readonly>

                                    @if(session('error_from') === 'edit_transaksi')
                                    @error('tanggal') <div class="text-danger">{{ $message }}</div> @enderror
                                    @endif
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Siswa</label>
                                    <select class="form-select" name="siswa_id" id="select-siswa">
                                        <option value="">-- Tidak Ada --</option>

                                        @foreach($siswa as $s)
                                        <option value="{{ $s->id }}"
                                            @if(session('error_from')==='edit_transaksi' )
                                            {{ old('siswa_id') == $s->id ? 'selected' : '' }}
                                            @else
                                            {{ $transaksi->siswa_id == $s->id ? 'selected' : '' }}
                                            @endif>
                                            {{ $s->nama }}
                                        </option>
                                        @endforeach
                                    </select>

                                    @if(session('error_from') === 'edit_transaksi')
                                    @error('siswa_id') <div class="text-danger">{{ $message }}</div> @enderror
                                    @endif
                                </div>
                            </div>


                            {{-- ============================
             HISTORY PEMBAYARAN
        ============================= --}}
                            <div id="history-box" class="mb-3">
                                <h6 class="mb-2">Riwayat Pembayaran Siswa</h6>
                                <div id="history-content"
                                    class="border rounded p-2"
                                    style="max-height:200px; overflow-y:auto; background:#fafafa;">
                                    <small class="text-muted">Memuat data...</small>
                                </div>
                            </div>


                            {{-- ============================
             NOMINAL PER JENIS
        ============================= --}}
                            <div id="nominal-wrapper" class="mb-3">
                                <label class="form-label fw-bold">Nominal per Jenis Pembayaran</label>

                                <div id="nominal-fields" class="row border rounded p-2" style="background:#fafafa;">

                                    @foreach($jenis_pembayaran as $jp)

                                        @php
                                            $id = $jp->id;

                                            // Value dari database (jika transaksi sudah punya nominal)
                                            $dbValue = isset($detail[$id])
                                                ? number_format($detail[$id], 0, ',', '.')
                                                : "";

                                            // Value dari old input ketika validasi error
                                            $oldValue = old("nominal.$id");

                                            /*
                                            =======================================================
                                            LOGIKA NILAI (YANG INI FIX 100%):
                                            =======================================================

                                            - Jika old NULL  → gunakan nilai database
                                            - Jika old "" (kosong) & ada DB → gunakan database
                                            - Jika old punya nilai → gunakan old
                                            - Jika tidak ada DB & old kosong → tetap kosong
                                            =======================================================
                                            */

                                            if ($oldValue === null) {
                                                // Tidak ada old → tampilkan database
                                                $value = $dbValue;
                                            }
                                            elseif ($oldValue === "" && $dbValue !== "") {
                                                // Old kosong tapi ada nilai database → kembali ke DB
                                                $value = $dbValue;
                                            }
                                            else {
                                                // Old ada nilai → tampilkan old
                                                $value = $oldValue;
                                            }
                                        @endphp

                                        @php
                                            $hasError = $errors->has("nominal.$id");
                                        @endphp

                                        @if(isset($detail[$id]) || old("nominal.$id") !== null || $hasError)
                                            <div class="col-md-4 mb-2 nominal-item" id="nominal-{{ $id }}">
                                                <label class="form-label">Nominal {{ $jp->nama_pembayaran }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp.</span>
                                                    <input 
                                                        type="text"
                                                        class="form-control input-nominal format-nominal"
                                                        name="nominal[{{ $id }}]"
                                                        value="{{ $value }}"
                                                        autocomplete="off">
                                                </div>

                                                @if($hasError)
                                                    <div class="text-danger mt-1">{{ $errors->first("nominal.$id") }}</div>
                                                @endif
                                            </div>
                                        @endif

                                    @endforeach
                                </div>


                                {{-- TOTAL --}}
                                <div id="total-wrapper" class="mt-3">
                                    <label class="form-label fw-bold">Total Nominal</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text"
                                            id="total-nominal-display"
                                            class="form-control"
                                            readonly
                                            value="{{ old('nominal_total', number_format($transaksi->nominal, 0, ',', '.')) }}">
                                    </div>
                                </div>
                            </div>


                            {{-- ============================
             METODE PEMBAYARAN
        ============================= --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Metode Pembayaran</label>
                                <select name="metode" class="form-select">

                                    <option value="tunai"
                                        @if(session('error_from')==='edit_transaksi' )
                                        {{ old('metode')=='tunai' ? 'selected' : '' }}
                                        @else
                                        {{ $transaksi->metode=='tunai' ? 'selected' : '' }}
                                        @endif>Tunai</option>

                                    <option value="transfer"
                                        @if(session('error_from')==='edit_transaksi' )
                                        {{ old('metode')=='transfer' ? 'selected' : '' }}
                                        @else
                                        {{ $transaksi->metode=='transfer' ? 'selected' : '' }}
                                        @endif>Transfer</option>

                                </select>

                                @if(session('error_from') === 'edit_transaksi')
                                @error('metode') <div class="text-danger">{{ $message }}</div> @enderror
                                @endif
                            </div>


                            {{-- ============================
             KETERANGAN
        ============================= --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan', $transaksi->keterangan) }}</textarea>

                                @if(session('error_from') === 'edit_transaksi')
                                @error('keterangan') <div class="text-danger">{{ $message }}</div> @enderror
                                @endif
                            </div>


                            {{-- BUTTON --}}
                            <div class="text-end">
                                <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Kembali</a>
                                <button class="btn btn-primary">Update</button>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
</div>

{{-- ======================================================
   SCRIPT: SIMPAN ERROR, OLD NOMINAL, DETAIL NOMINAL
====================================================== --}}
<script>
    window.nominalErrors = @json($errors->any() && session('error_from')==='edit_transaksi' ? $errors->getMessages() : []);
    window.oldNominal = @json(old('nominal', []));
    window.detailNominal = @json($detail);
</script>


{{-- ======================================================
   SCRIPT: SELECT2
====================================================== --}}
<script>
    $(function() {
        $('#select-siswa').select2({
            theme: "bootstrap-5",
        });
    });
</script>


{{-- ======================================================
   SCRIPT: LOAD HISTORY
====================================================== --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {

        let siswaID = "{{ $transaksi->siswa_id }}";
        const historyContent = document.getElementById("history-content");

        if (siswaID) {
            historyContent.innerHTML = "<small class='text-muted'>Memuat data...</small>";

            fetch("{{ url('/transaksi/history') }}/" + siswaID)
                .then(res => res.json())
                .then(data => {
                    historyContent.innerHTML = data.html;
                });
        }
    });
</script>


{{-- ======================================================
   SCRIPT: AUTO SELECT PREVIEW
====================================================== --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const checkboxes = document.querySelectorAll(".jenis-pembayaran-checkbox");
        const box = document.getElementById("selected-payment-box");
        const listBox = document.getElementById("selected-payment-list");

        function updatePreview() {
            let selected = [];

            checkboxes.forEach(cb => {
                if (cb.checked) selected.push(cb.nextElementSibling.innerText.trim());
            });

            if (selected.length > 0) {
                box.style.display = "block";
                listBox.innerHTML = selected
                    .map(x => `<span class="badge bg-primary me-1">${x}</span>`)
                    .join('');
            } else {
                box.style.display = "none";
                listBox.innerHTML = "";
            }
        }

        checkboxes.forEach(cb => cb.addEventListener("change", updatePreview));

        updatePreview();
    });
</script>


{{-- ======================================================
   SCRIPT: AUTO NOMINAL / TOTAL / KETERANGAN (FINAL FIXED)
====================================================== --}}
<script>
document.addEventListener("DOMContentLoaded", function() {

    const checkboxes = document.querySelectorAll(".jenis-pembayaran-checkbox");
    const nominalFields = document.getElementById("nominal-fields");
    const totalDisplay = document.getElementById("total-nominal-display");
    const keterangan = document.getElementById("keterangan");

    // ⭐ Penyimpan nilai terakhir user
    let savedNominal = {};

    // ------------------ Helper Format ------------------
    function parseNumber(str) {
        return Number(str.replace(/\./g, "")) || 0;
    }

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // ------------------ Tambahkan Input Nominal ------------------
    function addNominalInput(cb) {
        let jpID = cb.value;
        let jpLabel = cb.nextElementSibling.innerText.trim();

        // Kalau input sudah ada → tidak buat lagi
        if (document.querySelector(`input[name="nominal[${jpID}]"]`)) {
            return;
        }

        // =============================
        // FIX PENTING — DEFINISIKAN hasDB & oldValue
        // =============================
        let detailNominal = window.detailNominal || {};
        let oldNominal = window.oldNominal || {};

        let hasDB = detailNominal.hasOwnProperty(jpID);
        let oldValue = oldNominal[jpID] ?? null;
        // =============================

        let initialValue = "";

        // ★ 1. Jika ada nilai tersimpan akibat uncheck → check
        if (savedNominal[jpID] !== undefined && savedNominal[jpID] !== "") {
            initialValue = savedNominal[jpID];
        }

        // ★ 2. Jika jenis ADA di database
        else if (hasDB) {

            // Laravel mengirim null ketika user mengosongkan input → kembalikan DB
            if (oldValue === null || oldValue === "") {
                initialValue = new Intl.NumberFormat("id-ID")
                    .format(detailNominal[jpID]);
            }
            // Jika user isi angka → tetap pakai old
            else {
                initialValue = oldValue;
            }
        }

        // ★ 3. Jika TIDAK ADA di database (jenis baru)
        else {
            // Jika user isi angka → pakai old
            if (oldValue !== null && oldValue !== "") {
                initialValue = oldValue;
            }
            // Jika kosong → tetap kosong
            else {
                initialValue = "";
            }
        }

        // ⭐ Error validasi
        let errorKey = `nominal.${jpID}`;
        let errorHtml = "";
        if (window.nominalErrors && window.nominalErrors[errorKey]) {
            errorHtml = `<div class="text-danger mt-1">${window.nominalErrors[errorKey][0]}</div>`;
        }

        // ⭐ Buat elemen input
        let div = document.createElement("div");
        div.classList.add("col-md-4", "mb-2", "nominal-item");

        div.innerHTML = `
            <label class="form-label">Nominal ${jpLabel}</label>
            <div class="input-group">
                <span class="input-group-text">Rp.</span>
                <input type="text"
                    class="form-control input-nominal format-nominal"
                    name="nominal[${jpID}]"
                    value="${initialValue}"
                    autocomplete="off">
            </div>
            ${errorHtml}
        `;

        nominalFields.appendChild(div);
    }

    // ------------------ Hitung Total ------------------
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll(".input-nominal").forEach(inp => {
            total += parseNumber(inp.value);
        });
        totalDisplay.value = formatNumber(total);
    }

    // ------------------ Auto Keterangan ------------------
    function generateKeterangan() {
        let parts = [];

        checkboxes.forEach(cb => {
            if (cb.checked) {
                const label = cb.nextElementSibling.innerText.trim();
                const nominalInput = document.querySelector(`input[name="nominal[${cb.value}]"]`);
                let nominal = nominalInput ? parseNumber(nominalInput.value) : 0;

                if (nominal > 0) parts.push(`${label} (${formatNumber(nominal)})`);
                else parts.push(label);
            }
        });

        keterangan.value = parts.length ? "Pembayaran untuk: " + parts.join(", ") : "";
    }

    // ------------------ Event: Nominal berubah ------------------
    document.addEventListener("keyup", function(e) {
        if (e.target.classList.contains("input-nominal")) {

            // ⭐ SIMPAN nilai terakhir user
            let name = e.target.getAttribute("name"); // nominal[ID]
            let id = name.replace('nominal[', '').replace(']', '');
            savedNominal[id] = e.target.value;

            calculateTotal();
            generateKeterangan();
        }
    });

    // ------------------ Event: Checkbox berubah ------------------
    checkboxes.forEach(cb => cb.addEventListener("change", function() {

        if (cb.checked) {
            addNominalInput(cb);
        } else {

            // ⭐ SIMPAN nilai sebelum dihapus
            let inp = document.querySelector(`input[name="nominal[${cb.value}]"]`);
            if (inp) {
                savedNominal[cb.value] = inp.value;
                inp.closest(".nominal-item").remove();
            }
        }

        calculateTotal();
        generateKeterangan();

    }));

    // ------------------ Init pertama kali ------------------
    calculateTotal();
    generateKeterangan();

});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const selectSiswa = document.getElementById("select-siswa");
    const historyBox = document.getElementById("history-box");
    const historyContent = document.getElementById("history-content");

    function loadHistory(id) {

        if (!id) {
            historyBox.style.display = "none";
            return;
        }

        historyBox.style.display = "block";
        historyContent.innerHTML = "<small class='text-muted'>Memuat data...</small>";

        fetch("{{ url('/transaksi/history') }}/" + id)
            .then(res => res.json())
            .then(data => {
                historyContent.innerHTML = data.html;
            })
            .catch(() => {
                historyContent.innerHTML = "<small class='text-danger'>Gagal memuat data.</small>";
            });
    }

    // === JALANKAN SAAT HALAMAN EDIT DIBUKA ===
    loadHistory(selectSiswa.value);

    // === JALANKAN SAAT USER GANTI SISWA ===
    selectSiswa.addEventListener("change", function() {
        loadHistory(this.value);
    });

    // === JIKA PAKAI SELECT2, TETAP TANGKAP EVENT-NYA ===
    $('#select-siswa').on('select2:select', function (e) {
        loadHistory(e.params.data.id);
    });

});
</script>

{{-- ======================================================
   SCRIPT: FORMAT NOMINAL INPUT
====================================================== --}}
<script>
    document.addEventListener("input", function(e) {
        if (e.target.classList.contains("format-nominal")) {
            let value = e.target.value.replace(/\D/g, "");
            e.target.value = value ? new Intl.NumberFormat("id-ID").format(value) : "";
        }
    });
</script>

@endsection