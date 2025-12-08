<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Buku Kas Bulan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        h2,
        h3 {
            text-align: center;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 6px;
            font-size: 14px;
            text-align: center;
        }

        .signature {
            width: 100%;
            margin-top: 50px;
        }

        /* Tanggal berada di kanan atas */
        .signature .date {
            width: 45%;
            /* mengikuti lebar kolom kanan */
            text-align: center;
            /* posisikan ke tengah */
            margin-left: auto;
            /* dorong ke sisi kanan */
            margin-bottom: 20px;
        }

        /* Ketua dan Bendahara sejajar */
        .signature .row-sign {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .signature .left,
        .signature .right {
            width: 45%;
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>BUKU KAS BULAN {{ $namaBulan }} {{ $tahun }}</h2>
    <h3>Periode: {{ $periode }}</h3>

    <table>
        <tr>
            <th>Tgl.</th>
            <th>Uraian</th>
            <th>No. Bukti</th>
            <th>Penerimaan</th>
            <th>Pengeluaran</th>
            <th>Saldo</th>
        </tr>

        {{-- Tampilkan semua data tanpa limit --}}
        @foreach($rows as $r)
        <tr>
            <td>{{ $r['tanggal'] }}</td>
            <td>{{ $r['uraian'] }}</td>
            <td>{{ $r['no_bukti'] }}</td>
            <td>{{ $r['penerimaan'] }}</td>
            <td>{{ $r['pengeluaran'] }}</td>
            <td>{{ $r['saldo'] }}</td>
        </tr>
        @endforeach

    </table>

    <div class="signature">

        <div class="date">
            Lampung, {{ $today }}
        </div>

        <div class="row-sign">
            <div class="left">
                Mengetahui<br>
                Ketua<br><br><br><br>
                _______________________
            </div>

            <div class="right">
                Bendahara<br><br><br><br><br>
                _______________________
            </div>
        </div>

    </div>

</body>

</html>