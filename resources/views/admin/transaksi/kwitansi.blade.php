@php

if (!function_exists('terbilang')) {
function terbilang($number)
{
$number = abs($number);
$words = array("", "satu", "dua", "tiga", "empat", "lima", "enam",
"tujuh", "delapan", "sembilan", "sepuluh", "sebelas");

if ($number < 12) {
    return $words[$number];
    } elseif ($number < 20) {
    return terbilang($number - 10) . " belas" ;
    } elseif ($number < 100) {
    return terbilang(intval($number / 10)) . " puluh " . terbilang($number % 10);
    } elseif ($number < 200) {
    return "seratus " . terbilang($number - 100);
    } elseif ($number < 1000) {
    return terbilang(intval($number / 100)) . " ratus " . terbilang($number % 100);
    } elseif ($number < 2000) {
    return "seribu " . terbilang($number - 1000);
    } elseif ($number < 1000000) {
    return terbilang(intval($number / 1000)) . " ribu " . terbilang($number % 1000);
    } elseif ($number < 1000000000) {
    return terbilang(intval($number / 1000000)) . " juta " . terbilang($number % 1000000);
    } elseif ($number < 1000000000000) {
    return terbilang(intval($number / 1000000000)) . " milyar " . terbilang($number % 1000000000);
    } else {
    return "Angka terlalu besar" ;
    }
    }
    }

    @endphp

    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <title>Kwitansi Pembayaran</title>
        <style>
            body {
                font-family: Arial, sans-serif;
            }

            .border {
                display: table;
                width: 900px;
                border: 1px solid #000;
                padding: 10px;
                margin: auto;
            }

            .left {
                display: table-cell;
                width: 28%;
                border-right: 1px solid #000;
                padding-right: 10px;
                font-size: 13px;
                line-height: 20px;
            }

            .right {
                display: table-cell;
                padding-left: 15px;
            }

            .title {
                text-align: center;
                font-weight: bold;
                font-size: 17px;
                margin-bottom: 30px;
            }

            .line {
                display: inline-block;
                border-bottom: 1px dotted #000;
                width: 100%;
                height: 12px;
            }

            .rp-sign-wrapper {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }

            .rp-box {
                display: inline-block;
                padding: 8px 10px;
                border: 1px solid #000;
                transform: skew(-20deg);
                /* bikin miring */
                width: 200px;
            }

            .rp-text {
                transform: skew(20deg);
                /* mengembalikan teks agar tidak ikut miring */
                display: inline-block;
            }

            .sign {
                display: flex;
                gap: 40px;
                margin-top: 25px;
            }

            .sign div {
                text-align: center;
                width: 150px;
            }

            .space {
                margin-bottom: 10px;
            }
        </style>
    </head>

    <body>

        <div class="border">

            <!-- LEFT SIDE -->
            <div class="left">

                <div style="display: flex; gap: 3px;">
                    No:
                    <span class="">{{ $t->kode_transaksi }}</span>
                </div>

                <div style="display: flex; gap: 3px; margin-bottom: 10px;">
                    Tanggal:
                    <span class="">{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</span>
                </div>

                <div style="margin-bottom: 10px;">
                    Terima Dari: <br>
                    <span class="">{{ $t->siswa->nama ?? 'Bendahara' }}</span>
                </div>

                <div style="margin-bottom: 10px;">
                    Jumlah: <br>
                    <span class="">Rp {{ number_format($t->nominal, 0, ',', '.') }}</span>
                </div>

                <div style="margin-bottom: 10px;">
                    Jenis Pembayaran: <br>
                    <span class="">
                        {{ $t->tipe == 'masuk' ? 'PEMASUKAN' : 'PENGELUARAN' }}
                    </span>
                </div>

                Untuk Pembayaran: <br>
                <span class="">{{ $t->keterangan }}</span>
            </div>

            <!-- RIGHT SIDE -->
            <div class="right">

                <div class="title">KWITANSI PEMBAYARAN</div>

                <div class="space" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
                    <div style="width:48%;">
                        No: <span class="" style="width: 50%;">{{ $t->kode_transaksi }}</span>
                    </div>

                    <div style="width:48%; text-align:right;">
                        Tanggal: <span class="" style="width:50%;">{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y') }}</span>
                    </div>
                </div>

                <div class="space" style="display: flex; gap: 3px;">
                    Terima Dari: <span class="" style="width: 85%;">{{ $t->siswa->nama ?? 'Bendahara' }}</span>
                </div>

                <div class="space" style="display: flex; gap: 3px;">
                    Terbilang: <span class="" style="width: 87.6%;">{{ ucwords(terbilang($t->nominal)) }} Rupiah</span>
                </div>

                <div class="space" style="display: flex; gap: 3px;">
                    Untuk Pembayaran: <span class="" style="width: 76%;">{{ $t->keterangan }}</span>
                </div>

                <br>

                <div class="rp-sign-wrapper">

                    <div class="rp-box">
                        <span class="rp-text">RP. {{ number_format($t->nominal, 0, ',', '.') }}</span>
                    </div>

                    <div class="sign">
                        <div>
                            <span class="line" style="width:100%;"></span><br>
                            Tanda tangan Penerima
                        </div>

                        <div>
                            <span class="line" style="width:100%;"></span><br>
                            Tanda tangan Penyetor
                        </div>
                    </div>

                </div>

            </div>

            <div style="clear: both;"></div>
        </div>

    </body>

    <script>
        window.print();
    </script>


    </html>