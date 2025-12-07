<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kwitansi Pembayaran</title>

    <style>
        body {
            font-family: "Poppins", Arial, sans-serif;
            background: #eef2f9;
            padding: 40px;
        }

        .invoice {
            max-width: 650px;
            margin: auto;
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            color: #1e1e1e;
            box-shadow: 0 20px 50px rgba(0, 55, 150, 0.15);
            border: 1px solid #dbe4f3;
        }

        /* ---------------- HEADER ---------------- */
        .header {
            padding: 35px 40px;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: #fff;
        }

        .header-title {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .badge-type {
            margin-top: 14px;
            display: inline-block;
            padding: 7px 18px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #fff;
        }

        .masuk {
            background: rgba(46, 204, 112, 0.9);
        }

        .keluar {
            background: rgba(231, 76, 60, 0.9);
        }

        /* ---------------- CONTENT ---------------- */
        .content {
            padding: 35px 40px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .label {
            color: #66748a;
            font-size: 14px;
        }

        .value {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
        }

        /* ---------------- AMOUNT BOX ---------------- */
        .amount-box {
            margin-top: 35px;
            padding: 28px;
            background: linear-gradient(135deg, #f1f5ff, #dbe7ff);
            border: 1px solid #c7d7ff;
            border-radius: 16px;
            text-align: center;
        }

        .amount-title {
            font-size: 13px;
            color: #51617a;
        }

        .amount {
            margin-top: 8px;
            font-size: 32px;
            font-weight: 800;
            color: #1e40af;
        }

        .divider {
            margin: 40px 0;
            border-bottom: 1px dashed #c4ccdd;
        }

        /* ---------------- SIGNATURE ---------------- */
        .signature-block {
            text-align: right;
            margin-top: 20px;
        }

        .sig-line {
            width: 180px;
            border-bottom: 1px solid #1e1e1e;
            margin-left: auto;
            margin-top: 75px;
        }

        /* ---------------- FOOTER ---------------- */
        .footer {
            padding: 22px;
            background: #f6f8ff;
            font-size: 12px;
            color: #6c7a92;
            text-align: center;
            border-top: 1px solid #d7e0f5;
        }

        /* ---------------- PRINT BUTTON ---------------- */
        .print-btn {
            margin-bottom: 25px;
            padding: 12px 20px;
            background: #1e40af;
            color: #fff;
            border-radius: 8px;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }

        .print-btn:hover {
            background: #2539b6;
        }

        /* ========================================================= */
        /* ========== FIX PRINT: KEEP ALL COLORS & SHADOW ========== */
        /* ========================================================= */
        @media print {

            /* Pastikan warna & gradient tidak hilang */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            body {
                padding: 0 !important;
                background: #ffffff !important;
            }

            .print-btn {
                display: none !important;
            }

            /* ----- FORCE BOX SHADOW SAAT PRINT ----- */
            .invoice {
                box-shadow: 0 20px 50px rgba(0, 55, 150, 0.15) !important;
                border: 1px solid #dbe4f3 !important;

                /* Hack wajib untuk memaksa Chrome mencetak shadow */
                -webkit-filter: drop-shadow(0 20px 50px rgba(0, 55, 150, 0.15)) !important;
                filter: drop-shadow(0 20px 50px rgba(0, 55, 150, 0.15)) !important;
            }
        }
    </style>
</head>

<body>

    <div style="text-align:center;">
        <a onclick="window.print()" class="print-btn">Print Kwitansi</a>
    </div>

    <div class="invoice">

        <!-- HEADER -->
        <div class="header">
            <div class="header-title">Kwitansi Pembayaran</div>

            @if($t->tipe === 'masuk')
            <span class="badge-type masuk">Pemasukan</span>
            @else
            <span class="badge-type keluar">Pengeluaran</span>
            @endif
        </div>

        <!-- CONTENT -->
        <div class="content">

            <div class="info-row">
                <div class="label">Nomor Kwitansi</div>
                <div class="value">{{ $t->id }}</div>
            </div>

            <div class="info-row">
                <div class="label">Tanggal</div>
                <div class="value">{{ $t->tanggal }}</div>
            </div>

            <div class="info-row">
                <div class="label">Nama Siswa</div>
                <div class="value">{{ $t->siswa->nama ?? '-' }}</div>
            </div>

            <div class="info-row">
                <div class="label">Kelas</div>
                <div class="value">{{ $t->siswa->kelas->nama_kelas ?? '-' }}</div>
            </div>

            <div class="info-row">
                <div class="label">Metode Pembayaran</div>
                <div class="value">{{ ucfirst($t->metode) ?? '-' }}</div>
            </div>

            <div class="info-row">
                <div class="label">Deskripsi</div>
                <div class="value">{{ $t->deskripsi }}</div>
            </div>

            <!-- AMOUNT -->
            <div class="amount-box">
                <div class="amount-title">Total Pembayaran</div>
                <div class="amount">
                    Rp {{ number_format($t->nominal, 0, ',', '.') }}
                </div>
            </div>

            <div class="divider"></div>

            <!-- SIGNATURE -->
            <div class="signature-block">
                Lampung, {{ now()->format('d-m-Y') }}

                <div class="sig-line"></div>
                <div style="margin-top:6px; font-size:13px;">
                    Bendahara Sekolah
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            Dokumen ini dihasilkan otomatis oleh sistem dan sah tanpa tanda tangan.
        </div>

    </div>

</body>

</html>