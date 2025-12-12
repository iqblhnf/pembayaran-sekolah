<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisPembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = [
            ['nama_pembayaran' => 'Pengembangan sekolah'],
            ['nama_pembayaran' => 'Pengembangan mutu'],
            ['nama_pembayaran' => 'SPP'],
            ['nama_pembayaran' => 'Seragam'],
            ['nama_pembayaran' => 'Kaos'],
            ['nama_pembayaran' => 'OSIS kegiatan'],
            ['nama_pembayaran' => 'Pramuka kegiatan'],
            ['nama_pembayaran' => 'Asuransi'],
            ['nama_pembayaran' => 'Diksar militer'],
            ['nama_pembayaran' => 'PKS'],
            ['nama_pembayaran' => 'Diklat paskib'],
            ['nama_pembayaran' => 'Penerimaan tamu Ambalan'],
        ];

        DB::table('jenis_pembayarans')->insert($data);
    }
}
